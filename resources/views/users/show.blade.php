@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="container">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">← Back to Members</a>
    </div>

    <div class="grid grid-2" style="align-items:start;">
        <div class="card">
            <div class="card-body" style="text-align:center;padding:2rem;">
                <img src="{{ $user->avatar_url }}" style="width:80px;height:80px;border-radius:50%;margin-bottom:1rem;" alt="">
                <h2 style="font-size:1.25rem;font-weight:800;">{{ $user->name }}</h2>
                <p class="text-muted">{{ $user->email }}</p>
                <div style="margin:0.75rem 0;">
                    @foreach($user->roles as $role)
                        <span class="badge badge-{{ $role->name }}" style="font-size:0.85rem;padding:0.3rem 0.75rem;">{{ ucfirst($role->name) }}</span>
                    @endforeach
                </div>
                @if($user->is_active)
                    <span class="badge badge-available">Active Member</span>
                @else
                    <span class="badge badge-unavailable">Inactive</span>
                @endif

                <div style="margin-top:1.5rem;text-align:left;">
                    @if($user->phone)
                    <div style="display:flex;gap:0.5rem;margin-bottom:0.5rem;font-size:0.875rem;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" style="flex-shrink:0;color:var(--gray-400);"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 14 19.79 19.79 0 0 1 1.61 5.18 2 2 0 0 1 3.6 3h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 10.91a16 16 0 0 0 6.18 6.18"/></svg>
                        {{ $user->phone }}
                    </div>
                    @endif
                    @if($user->address)
                    <div style="display:flex;gap:0.5rem;margin-bottom:0.5rem;font-size:0.875rem;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" style="flex-shrink:0;color:var(--gray-400);"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $user->address }}
                    </div>
                    @endif
                    <div style="display:flex;gap:0.5rem;margin-bottom:0.5rem;font-size:0.875rem;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" style="flex-shrink:0;color:var(--gray-400);"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Member since {{ $user->created_at->format('M d, Y') }}
                    </div>
                </div>

                @php $unpaidFines = $user->total_fines; @endphp
                @if($unpaidFines > 0)
                <div class="alert alert-warning" style="margin-top:1rem;text-align:left;">
                    Outstanding fines: <strong>${{ number_format($unpaidFines, 2) }}</strong>
                </div>
                @endif

                @can('manage users')
                <div style="margin-top:1.5rem;">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-outline btn-block">Edit Member</a>
                </div>
                @endcan
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Borrowing History</h2>
                    <span class="text-muted text-small">{{ $borrowings->total() }} total</span>
                </div>
                <div class="card-body" style="padding:0;">
                    @if($borrowings->count())
                    <div class="table-wrapper">
                        <table>
                            <thead><tr><th>Book</th><th>Borrowed</th><th>Status</th><th>Fine</th></tr></thead>
                            <tbody>
                                @foreach($borrowings as $b)
                                <tr>
                                    <td><a href="{{ route('books.show', $b->book) }}" style="font-weight:600;font-size:0.875rem;">{{ Str::limit($b->book->title, 25) }}</a></td>
                                    <td class="text-muted text-small">{{ $b->borrowed_at->format('M d, Y') }}</td>
                                    <td><span class="badge badge-{{ $b->status }}">{{ ucfirst($b->status) }}</span></td>
                                    <td>
                                        @if($b->fine_amount > 0)
                                            <span class="{{ $b->fine_paid ? 'text-success' : 'text-danger' }}">
                                                ${{ number_format($b->fine_amount, 2) }}
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
                    <div class="empty-state" style="padding:2rem;"><p>No borrowing history.</p></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
