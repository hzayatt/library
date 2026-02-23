@extends('layouts.app')

@section('title', 'My Reading List')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">My Reading List</h1>
            <p class="page-subtitle">Books you want to read</p>
        </div>
        <a href="{{ route('books.index') }}" class="btn btn-primary">Browse More Books</a>
    </div>

    @if($items->count())
    <div class="grid grid-books">
        @foreach($items as $item)
        <div class="book-card">
            <div class="book-cover">
                <img src="{{ $item->book->cover_image_url }}" alt="{{ $item->book->title }}" loading="lazy">
                @if($item->book->is_available)
                    <span class="book-availability-badge badge badge-available">Available</span>
                @else
                    <span class="book-availability-badge badge badge-unavailable">Out</span>
                @endif
            </div>
            <div class="book-info">
                <div class="book-title">{{ $item->book->title }}</div>
                <div class="book-author">{{ $item->book->author }}</div>
                <div class="book-meta">
                    @if($item->book->genre) <span class="badge badge-genre">{{ $item->book->genre }}</span> @endif
                </div>
                <div class="book-actions">
                    <a href="{{ route('books.show', $item->book) }}" class="btn btn-outline btn-sm">View</a>
                    <form method="POST" action="{{ route('reading-list.toggle', $item->book) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Remove</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="pagination">{{ $items->links() }}</div>
    @else
    <div class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
        <h3>Your reading list is empty</h3>
        <p>Browse the catalog and bookmark books you want to read!</p>
        <div style="margin-top:1rem;"><a href="{{ route('books.index') }}" class="btn btn-primary">Browse Books</a></div>
    </div>
    @endif
</div>
@endsection
