<?php

namespace App\Http\Controllers;

use Anthropic\Client as AnthropicClient;
use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AiController extends Controller
{
    private function getClient(): AnthropicClient
    {
        return new AnthropicClient(config('services.anthropic.key'));
    }

    public function generateSummary(Book $book)
    {
        $this->authorize('update', $book);

        try {
            $client = $this->getClient();

            $prompt = "Please write a concise, engaging 2-3 sentence summary for the book \"{$book->title}\" by {$book->author}.";
            if ($book->description) {
                $prompt .= " Here's the description for context: {$book->description}";
            }
            $prompt .= " The summary should help library members decide if they want to read it.";

            $message = $client->messages->create(
                maxTokens: 500,
                messages: [['role' => 'user', 'content' => $prompt]],
                model: 'claude-3-5-haiku-20241022',
            );

            $summary = $message->content[0]->text;
            $book->update(['ai_summary' => $summary]);

            return response()->json(['success' => true, 'summary' => $summary]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'AI service unavailable. Please try again later.'], 500);
        }
    }

    public function generateTags(Book $book)
    {
        $this->authorize('update', $book);

        try {
            $client = $this->getClient();

            $prompt = "Generate 5-8 relevant tags/keywords for the book \"{$book->title}\" by {$book->author} (Genre: {$book->genre}). "
                . "Return ONLY a JSON array of strings, like: [\"tag1\", \"tag2\", \"tag3\"]";

            $message = $client->messages->create(
                maxTokens: 200,
                messages: [['role' => 'user', 'content' => $prompt]],
                model: 'claude-3-5-haiku-20241022',
            );

            $text = $message->content[0]->text;
            preg_match('/\[.*\]/s', $text, $matches);
            $tags = $matches ? json_decode($matches[0], true) : [];

            if (!is_array($tags)) $tags = [];

            $book->update(['ai_tags' => $tags]);

            return response()->json(['success' => true, 'tags' => $tags]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'AI service unavailable.'], 500);
        }
    }

    public function recommendations(Request $request)
    {
        $user = Auth::user();
        $cacheKey = "recommendations_{$user->id}";

        $recommendations = Cache::remember($cacheKey, 3600, function () use ($user) {
            $borrowedBooks = Borrowing::where('user_id', $user->id)
                ->with('book')
                ->latest()
                ->take(5)
                ->get()
                ->pluck('book')
                ->filter();

            if ($borrowedBooks->isEmpty()) {
                return null;
            }

            $bookList = $borrowedBooks
                ->map(fn($b) => "\"{$b->title}\" by {$b->author} (Genre: {$b->genre})")
                ->join(', ');

            $availableBooks = Book::available()
                ->whereNotIn('id', $borrowedBooks->pluck('id'))
                ->take(20)
                ->get()
                ->map(fn($b) => "ID:{$b->id} \"{$b->title}\" by {$b->author} (Genre: {$b->genre})")
                ->join("\n");

            if (empty($availableBooks)) {
                return null;
            }

            try {
                $client = $this->getClient();

                $prompt = "Based on books this user has borrowed: {$bookList}\n\n"
                    . "From these available books:\n{$availableBooks}\n\n"
                    . "Recommend 3 books. Return ONLY a JSON array of book IDs and reasons: "
                    . "[{\"id\": 1, \"reason\": \"brief reason\"}, ...]";

                $message = $client->messages->create(
                    maxTokens: 400,
                    messages: [['role' => 'user', 'content' => $prompt]],
                    model: 'claude-3-5-haiku-20241022',
                );

                $text = $message->content[0]->text;
                preg_match('/\[.*\]/s', $text, $matches);
                $recs = $matches ? json_decode($matches[0], true) : [];

                if (!is_array($recs)) return null;

                $bookIds = collect($recs)->pluck('id');
                $books = Book::whereIn('id', $bookIds)->get()->keyBy('id');

                return collect($recs)->map(function ($rec) use ($books) {
                    $book = $books->get($rec['id'] ?? 0);
                    return $book ? ['book' => $book, 'reason' => $rec['reason'] ?? ''] : null;
                })->filter()->values();

            } catch (\Exception $e) {
                return null;
            }
        });

        return view('ai.recommendations', compact('recommendations', 'user'));
    }

    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        $userMessage = $request->input('message');
        $totalBooks = Book::count();
        $availableBooks = Book::available()->count();
        $genres = Book::whereNotNull('genre')->distinct()->pluck('genre')->join(', ');

        $system = "You are a helpful library assistant. The library has {$totalBooks} books total, "
            . "{$availableBooks} currently available. Genres: {$genres}. "
            . "Help users find books, answer questions, and provide recommendations. Be concise and friendly.";

        try {
            $client = $this->getClient();

            $message = $client->messages->create(
                maxTokens: 600,
                messages: [['role' => 'user', 'content' => $userMessage]],
                model: 'claude-3-5-haiku-20241022',
                system: $system,
            );

            return response()->json([
                'success'  => true,
                'response' => $message->content[0]->text,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'  => false,
                'response' => "I'm having trouble connecting right now. Please try again later.",
            ], 500);
        }
    }
}
