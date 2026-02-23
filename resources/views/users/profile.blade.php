@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container" style="max-width:900px;">
    <div class="page-header">
        <h1 class="page-title">My Profile</h1>
    </div>

    <div class="grid grid-2" style="align-items:start;">
        <div class="card">
            <div class="card-header"><h2 class="card-title">Profile Information</h2></div>
            <div class="card-body">
                <div style="text-align:center;margin-bottom:1.5rem;">
                    <img src="{{ $user->avatar_url }}" style="width:80px;height:80px;border-radius:50%;" alt="">
                    <div style="margin-top:0.75rem;">
                        @foreach($user->roles as $role)
                            <span class="badge badge-{{ $role->name }}" style="font-size:0.85rem;">{{ ucfirst($role->name) }}</span>
                        @endforeach
                    </div>
                    @if($user->google_id)
                    <div style="color:var(--gray-400);font-size:0.8rem;margin-top:0.5rem;">
                        <svg viewBox="0 0 24 24" width="14" style="vertical-align:middle;"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        Connected with Google
                    </div>
                    @endif
                </div>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PUT')
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ $user->email }}" disabled style="background:var(--gray-50);cursor:not-allowed;">
                        <p class="form-hint">Email cannot be changed here.</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+1 234 567 8900">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $user->address) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>

        <div>
            <div class="card mb-3">
                <div class="card-header">
                    <h2 class="card-title">Account Stats</h2>
                </div>
                <div class="card-body">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;text-align:center;">
                        <div style="padding:1rem;background:var(--gray-50);border-radius:var(--radius);">
                            <div style="font-size:1.75rem;font-weight:800;color:var(--primary);">{{ $user->borrowings()->count() }}</div>
                            <div class="text-muted text-small">Total Loans</div>
                        </div>
                        <div style="padding:1rem;background:var(--gray-50);border-radius:var(--radius);">
                            <div style="font-size:1.75rem;font-weight:800;color:var(--success);">{{ $user->activeBorrowings()->count() }}</div>
                            <div class="text-muted text-small">Active Loans</div>
                        </div>
                        <div style="padding:1rem;background:var(--gray-50);border-radius:var(--radius);">
                            <div style="font-size:1.75rem;font-weight:800;color:var(--warning);">{{ $user->reviews()->count() }}</div>
                            <div class="text-muted text-small">Reviews Written</div>
                        </div>
                        <div style="padding:1rem;background:var(--gray-50);border-radius:var(--radius);">
                            <div style="font-size:1.75rem;font-weight:800;color:{{ $user->total_fines > 0 ? 'var(--danger)' : 'var(--success)' }};">
                                ${{ number_format($user->total_fines, 2) }}
                            </div>
                            <div class="text-muted text-small">Unpaid Fines</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Recent Activity</h2>
                    <a href="{{ route('borrowings.my') }}" class="btn btn-outline btn-sm">View All</a>
                </div>
                <div class="card-body" style="padding:0;">
                    @if($recentBorrowings->count())
                    <div class="table-wrapper">
                        <table>
                            <tbody>
                                @foreach($recentBorrowings as $b)
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:0.5rem;">
                                            <img src="{{ $b->book->cover_image_url }}" style="width:28px;height:36px;object-fit:cover;border-radius:3px;" alt="">
                                            <div>
                                                <a href="{{ route('books.show', $b->book) }}" style="font-size:0.875rem;font-weight:600;">{{ Str::limit($b->book->title, 22) }}</a>
                                                <div class="text-muted text-small">{{ $b->borrowed_at->format('M d, Y') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-{{ $b->status }}">{{ ucfirst($b->status) }}</span></td>
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
        </div>
    </div>
</div>
@endsection
