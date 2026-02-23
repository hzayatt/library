<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_books'       => Book::count(),
            'available_books'   => Book::available()->count(),
            'total_borrowings'  => Borrowing::count(),
            'active_borrowings' => Borrowing::whereIn('status', ['active', 'overdue'])->count(),
            'overdue_borrowings'=> Borrowing::where('status', 'overdue')->count(),
            'total_users'       => User::count(),
        ];

        $recentBorrowings = Borrowing::with(['user', 'book'])
            ->latest()
            ->take(5)
            ->get();

        $overdueBorrowings = Borrowing::with(['user', 'book'])
            ->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->where('status', 'active')
                  ->where('due_at', '<', now());
            })
            ->latest('due_at')
            ->take(5)
            ->get();

        $popularBooks = Book::withCount(['borrowings'])
            ->orderByDesc('borrowings_count')
            ->take(5)
            ->get();

        // For members: their own borrowings
        $myBorrowings = null;
        $myReadingList = null;
        if ($user->hasRole('member')) {
            $myBorrowings = Borrowing::with('book')
                ->where('user_id', $user->id)
                ->whereIn('status', ['active', 'overdue'])
                ->get();
            $myReadingList = $user->readingList()->with('book')->latest()->take(3)->get();
        }

        return view('dashboard', compact(
            'stats', 'recentBorrowings', 'overdueBorrowings',
            'popularBooks', 'myBorrowings', 'myReadingList'
        ));
    }
}
