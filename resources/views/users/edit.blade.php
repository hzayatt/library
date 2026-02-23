@extends('layouts.app')

@section('title', 'Edit: ' . $user->name)

@section('content')
<div class="container" style="max-width:700px;">
    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary btn-sm">← Back</a>
    </div>
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Edit Member</h1>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf @method('PUT')
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Name <span>*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span>*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+1 234 567 8900">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role <span>*</span></label>
                        <select name="role" class="form-control" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Member's address">{{ old('address', $user->address) }}</textarea>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                        <label for="isActive">Account is active</label>
                    </div>
                </div>
                <div style="display:flex;gap:1rem;align-items:center;">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">Cancel</a>
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="ms-auto" onsubmit="return confirm('Are you sure you want to delete this member?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Member</button>
                    </form>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
