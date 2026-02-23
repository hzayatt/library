@extends('layouts.app')

@section('title', 'Borrowings')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Borrowings</h1>
            <p class="page-subtitle">Manage all library loans</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('borrowings.index') }}">
        <div class="search-bar">
            <div class="search-input-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" class="form-control search-input" placeholder="Search by member or book..." value="{{ request('search') }}">
            </div>
            <select name="status" class="form-control filter-select">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </div>
    </form>

    <div class="card">
        <div class="card-body" style="padding:0;">
            @if($borrowings->count())
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Book</th>
                            <th>Borrowed</th>
                            <th>Due Date</th>
                            <th>Returned</th>
                            <th>Status</th>
                            <th>Fine</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($borrowings as $b)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:0.5rem;">
                                    <img src="{{ $b->user->avatar_url }}" style="width:28px;height:28px;border-radius:50%;flex-shrink:0;" alt="">
                                    <div>
                                        <div style="font-weight:600;font-size:0.875rem;">{{ $b->user->name }}</div>
                                        <div class="text-muted text-small">{{ $b->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('books.show', $b->book) }}" style="font-weight:600;">{{ Str::limit($b->book->title, 30) }}</a>
                                <div class="text-muted text-small">{{ $b->book->author }}</div>
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
                                        @if($b->fine_paid) <small>(paid)</small> @endif
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
                                    @if($b->status !== 'returned')
                                        @can('checkin books')
                                        <form method="POST" action="{{ route('borrowings.checkin', $b) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm">Check In</button>
                                        </form>
                                        @endcan
                                        @can('manage borrowings')
                                        <form method="POST" action="{{ route('borrowings.renew', $b) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-outline btn-sm">Renew</button>
                                        </form>
                                        @endcan
                                    @endif
                                    @can('manage borrowings')
                                    @if($b->fine_amount > 0 && !$b->fine_paid)
                                    <form method="POST" action="{{ route('borrowings.pay-fine', $b) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Pay Fine</button>
                                    </form>
                                    @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $borrowings->links() }}
            </div>
            @else
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                <h3>No borrowings found</h3>
                <p>No records match your filter criteria.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
