@extends('layouts.app')

@section('title', 'My Borrowings')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">My Borrowings</h1>
            <p class="page-subtitle">Your complete borrowing history</p>
        </div>
        <a href="{{ route('books.index') }}" class="btn btn-primary">Browse Books</a>
    </div>

    @if(auth()->user()->total_fines > 0)
    <div class="alert alert-warning">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        You have <strong>${{ number_format(auth()->user()->total_fines, 2) }}</strong> in unpaid fines. Please visit the library to settle.
    </div>
    @endif

    <div class="card">
        <div class="card-body" style="padding:0;">
            @if($borrowings->count())
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr><th>Book</th><th>Borrowed</th><th>Due Date</th><th>Returned</th><th>Status</th><th>Fine</th></tr>
                    </thead>
                    <tbody>
                        @foreach($borrowings as $b)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:0.75rem;">
                                    <img src="{{ $b->book->cover_image_url }}" style="width:36px;height:48px;object-fit:cover;border-radius:4px;flex-shrink:0;" alt="">
                                    <div>
                                        <a href="{{ route('books.show', $b->book) }}" class="fw-bold" style="font-size:0.875rem;">{{ $b->book->title }}</a>
                                        <div class="text-muted text-small">{{ $b->book->author }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $b->borrowed_at->format('M d, Y') }}</td>
                            <td class="{{ $b->due_at->isPast() && $b->status !== 'returned' ? 'text-danger fw-bold' : '' }}">
                                {{ $b->due_at->format('M d, Y') }}
                            </td>
                            <td>{{ $b->returned_at ? $b->returned_at->format('M d, Y') : '—' }}</td>
                            <td><span class="badge badge-{{ $b->status }}">{{ ucfirst($b->status) }}</span></td>
                            <td>
                                @if($b->fine_amount > 0)
                                <span class="{{ $b->fine_paid ? 'text-success' : 'text-danger' }} fw-bold">
                                    ${{ number_format($b->fine_amount, 2) }}
                                    @if($b->fine_paid) <br><small class="text-success">Paid</small> @endif
                                </span>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $borrowings->links() }}</div>
            @else
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                <h3>No borrowing history</h3>
                <p>You haven't borrowed any books yet.</p>
                <div style="margin-top:1rem;"><a href="{{ route('books.index') }}" class="btn btn-primary">Browse Books</a></div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
