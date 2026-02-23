<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\ReadingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReadingListController extends Controller
{
    public function index()
    {
        $items = ReadingList::with('book')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('reading-list.index', compact('items'));
    }

    public function toggle(Book $book)
    {
        $existing = ReadingList::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Removed from your reading list.';
        } else {
            ReadingList::create(['user_id' => Auth::id(), 'book_id' => $book->id]);
            $message = 'Added to your reading list!';
        }

        return back()->with('success', $message);
    }
}
