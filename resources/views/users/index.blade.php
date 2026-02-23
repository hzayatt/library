@extends('layouts.app')

@section('title', 'Members')

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Members</h1>
            <p class="page-subtitle">Manage library members and staff</p>
        </div>
    </div>

    <form method="GET" action="{{ route('users.index') }}">
        <div class="search-bar">
            <div class="search-input-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" class="form-control search-input" placeholder="Search by name or email..." value="{{ request('search') }}">
            </div>
            <select name="role" class="form-control filter-select">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </div>
    </form>

    <div class="card">
        <div class="card-body" style="padding:0;">
            @if($users->count())
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr><th>User</th><th>Role</th><th>Active Loans</th><th>Fines</th><th>Status</th><th>Joined</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:0.75rem;">
                                    <img src="{{ $user->avatar_url }}" style="width:36px;height:36px;border-radius:50%;flex-shrink:0;" alt="">
                                    <div>
                                        <div style="font-weight:600;">{{ $user->name }}</div>
                                        <div class="text-muted text-small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge badge-{{ $role->name }}">{{ ucfirst($role->name) }}</span>
                                @endforeach
                            </td>
                            <td>{{ $user->activeBorrowings()->count() }}</td>
                            <td>
                                @php $fines = $user->total_fines; @endphp
                                @if($fines > 0)
                                    <span class="text-danger fw-bold">${{ number_format($fines, 2) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge badge-available">Active</span>
                                @else
                                    <span class="badge badge-unavailable">Inactive</span>
                                @endif
                            </td>
                            <td class="text-muted text-small">{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <div style="display:flex;gap:0.4rem;">
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-outline btn-sm">View</a>
                                    @can('manage users')
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $users->links() }}</div>
            @else
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <h3>No users found</h3>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
