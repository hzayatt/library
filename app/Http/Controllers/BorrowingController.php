<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        $query = Borrowing::with(['user', 'book', 'issuedBy']);

        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->get('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        if ($search = $request->get('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%"))
            ->orWhereHas('book', fn($q) => $q->where('title', 'like', "%$search%"));
        }

        // Update overdue statuses
        $activeBorrowings = Borrowing::where('status', 'active')
            ->where('due_at', '<', now())
            ->get();
        foreach ($activeBorrowings as $b) {
            $b->updateOverdueStatus();
        }

        $borrowings = $query->latest()->paginate(15)->withQueryString();

        return view('borrowings.index', compact('borrowings'));
    }

    public function checkout(Request $request, Book $book)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'notes'   => 'nullable|string|max:500',
        ]);

        $targetUser = User::findOrFail($validated['user_id']);

        if ($book->available_copies <= 0) {
            return back()->with('error', 'No copies available for checkout.');
        }

        $existingBorrowing = Borrowing::where('user_id', $targetUser->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['active', 'overdue'])
            ->exists();

        if ($existingBorrowing) {
            return back()->with('error', 'This user already has this book checked out.');
        }

        $borrowing = Borrowing::create([
            'user_id'     => $targetUser->id,
            'book_id'     => $book->id,
            'issued_by'   => Auth::id(),
            'borrowed_at' => now(),
            'due_at'      => now()->addDays($book->borrow_days),
            'status'      => 'active',
            'notes'       => $validated['notes'] ?? null,
        ]);

        $book->decrement('available_copies');

        return redirect()->route('books.show', $book)
            ->with('success', "Book checked out to {$targetUser->name}. Due: {$borrowing->due_at->format('M d, Y')}");
    }

    public function checkin(Borrowing $borrowing)
    {
        if ($borrowing->status === 'returned') {
            return back()->with('error', 'This book has already been returned.');
        }

        $fine = $borrowing->calculateFine();

        $borrowing->update([
            'returned_at' => now(),
            'status'      => 'returned',
            'returned_to' => Auth::id(),
            'fine_amount' => $fine,
        ]);

        $borrowing->book->increment('available_copies');

        $message = 'Book checked in successfully!';
        if ($fine > 0) {
            $message .= " Fine assessed: \${$fine}";
        }

        return back()->with('success', $message);
    }

    public function renewBorrowing(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'active') {
            return back()->with('error', 'Only active borrowings can be renewed.');
        }

        $borrowing->update([
            'due_at' => $borrowing->due_at->addDays($borrowing->book->borrow_days),
        ]);

        return back()->with('success', 'Borrowing renewed! New due date: ' . $borrowing->fresh()->due_at->format('M d, Y'));
    }

    public function payFine(Borrowing $borrowing)
    {
        $borrowing->update(['fine_paid' => true]);
        return back()->with('success', 'Fine marked as paid.');
    }

    public function myBorrowings()
    {
        $borrowings = Borrowing::with('book')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('borrowings.my', compact('borrowings'));
    }
}
