<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookReviewController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        BookReview::updateOrCreate(
            ['user_id' => Auth::id(), 'book_id' => $book->id],
            $validated
        );

        return back()->with('success', 'Review submitted!');
    }

    public function destroy(BookReview $review)
    {
        if ($review->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
