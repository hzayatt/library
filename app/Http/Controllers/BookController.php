<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookReview;
use App\Models\Borrowing;
use App\Models\ReadingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::withCount(['borrowings', 'reviews'])
            ->withAvg('reviews', 'rating');

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        if ($genre = $request->get('genre')) {
            $query->byGenre($genre);
        }

        if ($request->get('available')) {
            $query->available();
        }

        $sortBy = $request->get('sort', 'title');
        $sortDir = $request->get('dir', 'asc');
        $allowedSorts = ['title', 'author', 'publication_year', 'available_copies', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDir === 'desc' ? 'desc' : 'asc');
        }

        $books = $query->paginate(12)->withQueryString();
        $genres = Book::whereNotNull('genre')->distinct()->pluck('genre')->sort()->values();

        return view('books.index', compact('books', 'genres'));
    }

    public function create()
    {
        $this->authorize('create', Book::class);
        return view('books.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Book::class);

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'author'           => 'required|string|max:255',
            'isbn'             => 'nullable|string|unique:books,isbn',
            'publisher'        => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1|max:' . (date('Y') + 1),
            'genre'            => 'nullable|string|max:100',
            'description'      => 'nullable|string',
            'language'         => 'nullable|string|max:50',
            'pages'            => 'nullable|integer|min:1',
            'cover_image'      => 'nullable|image|max:2048',
            'total_copies'     => 'required|integer|min:1',
            'fine_per_day'     => 'nullable|numeric|min:0',
            'borrow_days'      => 'nullable|integer|min:1',
        ]);

        $validated['available_copies'] = $validated['total_copies'];
        $validated['added_by'] = Auth::id();

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $book = Book::create($validated);

        return redirect()->route('books.show', $book)
            ->with('success', 'Book added successfully!');
    }

    public function show(Book $book)
    {
        $book->load(['reviews.user', 'addedBy']);
        $activeBorrowings = Borrowing::where('book_id', $book->id)
            ->whereIn('status', ['active', 'overdue'])
            ->with('user')
            ->get();

        $userReview = null;
        $inReadingList = false;
        $userActiveBorrowing = null;

        if (Auth::check()) {
            $userReview = BookReview::where('user_id', Auth::id())
                ->where('book_id', $book->id)
                ->first();
            $inReadingList = ReadingList::where('user_id', Auth::id())
                ->where('book_id', $book->id)
                ->exists();
            $userActiveBorrowing = Borrowing::where('user_id', Auth::id())
                ->where('book_id', $book->id)
                ->whereIn('status', ['active', 'overdue'])
                ->first();
        }

        return view('books.show', compact(
            'book', 'activeBorrowings', 'userReview', 'inReadingList', 'userActiveBorrowing'
        ));
    }

    public function edit(Book $book)
    {
        $this->authorize('update', $book);
        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $this->authorize('update', $book);

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'author'           => 'required|string|max:255',
            'isbn'             => 'nullable|string|unique:books,isbn,' . $book->id,
            'publisher'        => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1|max:' . (date('Y') + 1),
            'genre'            => 'nullable|string|max:100',
            'description'      => 'nullable|string',
            'language'         => 'nullable|string|max:50',
            'pages'            => 'nullable|integer|min:1',
            'cover_image'      => 'nullable|image|max:2048',
            'total_copies'     => 'required|integer|min:1',
            'fine_per_day'     => 'nullable|numeric|min:0',
            'borrow_days'      => 'nullable|integer|min:1',
        ]);

        // Adjust available copies based on total change
        $diff = $validated['total_copies'] - $book->total_copies;
        $validated['available_copies'] = max(0, $book->available_copies + $diff);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image && !str_starts_with($book->cover_image, 'http')) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $book->update($validated);

        return redirect()->route('books.show', $book)
            ->with('success', 'Book updated successfully!');
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        if ($book->activeBorrowing()->exists()) {
            return back()->with('error', 'Cannot delete a book that is currently borrowed.');
        }

        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Book deleted successfully!');
    }
}
