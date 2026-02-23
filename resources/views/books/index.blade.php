@extends('layouts.app')

@section('title', 'Books')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Library Catalog</h1>
            <p class="page-subtitle">{{ $books->total() }} books in the collection</p>
        </div>
        <div class="page-actions">
            @can('create books')
            <a href="{{ route('books.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Book
            </a>
            @endcan
        </div>
    </div>

    {{-- Search & Filters --}}
    <form method="GET" action="{{ route('books.index') }}">
        <div class="search-bar">
            <div class="search-input-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" class="form-control search-input" placeholder="Search by title, author, ISBN..." value="{{ request('search') }}">
            </div>
            <select name="genre" class="form-control filter-select">
                <option value="">All Genres</option>
                @foreach($genres as $genre)
                    <option value="{{ $genre }}" {{ request('genre') === $genre ? 'selected' : '' }}>{{ $genre }}</option>
                @endforeach
            </select>
            <select name="sort" class="form-control filter-select">
                <option value="title" {{ request('sort') === 'title' ? 'selected' : '' }}>Sort: Title</option>
                <option value="author" {{ request('sort') === 'author' ? 'selected' : '' }}>Sort: Author</option>
                <option value="publication_year" {{ request('sort') === 'publication_year' ? 'selected' : '' }}>Sort: Year</option>
                <option value="available_copies" {{ request('sort') === 'available_copies' ? 'selected' : '' }}>Sort: Availability</option>
                <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Sort: Recently Added</option>
            </select>
            <label class="form-check" style="white-space:nowrap;">
                <input type="checkbox" name="available" value="1" {{ request('available') ? 'checked' : '' }}>
                <span>Available only</span>
            </label>
            <button type="submit" class="btn btn-primary">Search</button>
            @if(request()->hasAny(['search', 'genre', 'available', 'sort']))
            <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </div>
    </form>

    @if($books->count())
    <div class="grid grid-books">
        @foreach($books as $book)
        <a href="{{ route('books.show', $book) }}" class="book-card" style="text-decoration:none;color:inherit;">
            <div class="book-cover">
                <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}" loading="lazy">
                @if($book->is_available)
                    <span class="book-availability-badge badge badge-available">Available</span>
                @else
                    <span class="book-availability-badge badge badge-unavailable">Out</span>
                @endif
            </div>
            <div class="book-info">
                <div class="book-title">{{ $book->title }}</div>
                <div class="book-author">{{ $book->author }}</div>
                <div class="book-meta">
                    @if($book->genre)
                        <span class="badge badge-genre">{{ $book->genre }}</span>
                    @endif
                    @if($book->publication_year)
                        <span class="text-muted text-small">{{ $book->publication_year }}</span>
                    @endif
                </div>
                @if($book->reviews_avg_rating)
                <div class="book-rating" style="margin-top:0.4rem;">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="13"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    {{ number_format($book->reviews_avg_rating, 1) }}
                    <span class="text-muted">({{ $book->reviews_count }})</span>
                </div>
                @endif
                <div style="margin-top:0.5rem; font-size:0.75rem; color:var(--gray-500);">
                    {{ $book->available_copies }}/{{ $book->total_copies }} copies
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div class="pagination">
        {{ $books->links() }}
    </div>
    @else
    <div class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        <h3>No books found</h3>
        <p>Try adjusting your search terms or filters.</p>
        @can('create books')
        <div style="margin-top:1rem;"><a href="{{ route('books.create') }}" class="btn btn-primary">Add First Book</a></div>
        @endcan
    </div>
    @endif
</div>
@endsection
