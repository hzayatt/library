@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
        @can('create books')
        <div class="page-actions">
            <a href="{{ route('books.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Book
            </a>
        </div>
        @endcan
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon--blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_books'] }}</div>
                <div class="stat-label">Total Books</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['available_books'] }}</div>
                <div class="stat-label">Available</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon--yellow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['active_borrowings'] }}</div>
                <div class="stat-label">Active Loans</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon--red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['overdue_borrowings'] }}</div>
                <div class="stat-label">Overdue</div>
            </div>
        </div>
        @can('view users')
        <div class="stat-card">
            <div class="stat-icon stat-icon--purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_users'] }}</div>
                <div class="stat-label">Members</div>
            </div>
        </div>
        @endcan
        <div class="stat-card">
            <div class="stat-icon stat-icon--cyan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_borrowings'] }}</div>
                <div class="stat-label">Total Loans</div>
            </div>
        </div>
    </div>

    {{-- Member: My Active Borrowings --}}
    @if($myBorrowings && $myBorrowings->count())
    <div class="card mb-4">
        <div class="card-header">
            <h2 class="card-title">My Currently Borrowed Books</h2>
            <a href="{{ route('borrowings.my') }}" class="btn btn-outline btn-sm">View All</a>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr><th>Book</th><th>Borrowed</th><th>Due Date</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($myBorrowings as $b)
                        <tr>
                            <td><a href="{{ route('books.show', $b->book) }}">{{ $b->book->title }}</a></td>
                            <td>{{ $b->borrowed_at->format('M d, Y') }}</td>
                            <td class="{{ $b->due_at->isPast() ? 'text-danger fw-bold' : '' }}">
                                {{ $b->due_at->format('M d, Y') }}
                                @if($b->due_at->isPast()) <span class="badge badge-overdue">Overdue</span> @endif
                            </td>
                            <td><span class="badge badge-{{ $b->status }}">{{ ucfirst($b->status) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <div class="dashboard-grid">
        {{-- Recent Borrowings --}}
        @can('view borrowings')
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Recent Borrowings</h2>
                <a href="{{ route('borrowings.index') }}" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div class="card-body" style="padding:0;">
                @if($recentBorrowings->count())
                <div class="table-wrapper">
                    <table>
                        <thead><tr><th>Member</th><th>Book</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($recentBorrowings as $b)
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:0.5rem;">
                                        <img src="{{ $b->user->avatar_url }}" style="width:28px;height:28px;border-radius:50%;" alt="">
                                        {{ $b->user->name }}
                                    </div>
                                </td>
                                <td>{{ Str::limit($b->book->title, 25) }}</td>
                                <td><span class="badge badge-{{ $b->status }}">{{ ucfirst($b->status) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state" style="padding:2rem;">
                    <p>No borrowings yet.</p>
                </div>
                @endif
            </div>
        </div>
        @endcan

        {{-- Overdue Books --}}
        @can('view borrowings')
        <div class="card">
            <div class="card-header">
                <h2 class="card-title" style="color:var(--danger);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" style="vertical-align:middle;margin-right:0.25rem;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Overdue Books
                </h2>
                <a href="{{ route('borrowings.index', ['status' => 'overdue']) }}" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div class="card-body" style="padding:0;">
                @if($overdueBorrowings->count())
                <div class="table-wrapper">
                    <table>
                        <thead><tr><th>Member</th><th>Book</th><th>Days Late</th></tr></thead>
                        <tbody>
                            @foreach($overdueBorrowings as $b)
                            <tr>
                                <td>{{ $b->user->name }}</td>
                                <td>{{ Str::limit($b->book->title, 22) }}</td>
                                <td class="text-danger fw-bold">{{ now()->diffInDays($b->due_at) }}d</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state" style="padding:2rem;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    <p>No overdue books!</p>
                </div>
                @endif
            </div>
        </div>
        @endcan

        {{-- Popular Books --}}
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Most Popular Books</h2>
                <a href="{{ route('books.index', ['sort' => 'created_at', 'dir' => 'desc']) }}" class="btn btn-outline btn-sm">All Books</a>
            </div>
            <div class="card-body" style="padding:0;">
                @if($popularBooks->count())
                <div class="table-wrapper">
                    <table>
                        <thead><tr><th>Book</th><th>Author</th><th>Loans</th></tr></thead>
                        <tbody>
                            @foreach($popularBooks as $book)
                            <tr>
                                <td><a href="{{ route('books.show', $book) }}">{{ Str::limit($book->title, 25) }}</a></td>
                                <td class="text-muted">{{ $book->author }}</td>
                                <td><strong>{{ $book->borrowings_count }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state" style="padding:2rem;"><p>No borrowing history yet.</p></div>
                @endif
            </div>
        </div>

        {{-- Reading List for members --}}
        @if($myReadingList !== null)
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">My Reading List</h2>
                <a href="{{ route('reading-list.index') }}" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div class="card-body" style="padding:0;">
                @if($myReadingList->count())
                <div class="table-wrapper">
                    <table>
                        <tbody>
                            @foreach($myReadingList as $item)
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:0.75rem;">
                                        <img src="{{ $item->book->cover_image_url }}" style="width:36px;height:48px;object-fit:cover;border-radius:4px;" alt="">
                                        <div>
                                            <a href="{{ route('books.show', $item->book) }}" class="fw-bold" style="font-size:0.875rem;">{{ Str::limit($item->book->title, 28) }}</a>
                                            <div class="text-muted text-small">{{ $item->book->author }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($item->book->is_available)
                                        <span class="badge badge-available">Available</span>
                                    @else
                                        <span class="badge badge-unavailable">Out</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state" style="padding:2rem;"><p>Your reading list is empty. <a href="{{ route('books.index') }}">Browse books</a></p></div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
