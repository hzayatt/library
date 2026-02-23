<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Book $book): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create books');
    }

    public function update(User $user, Book $book): bool
    {
        return $user->hasPermissionTo('edit books');
    }

    public function delete(User $user, Book $book): bool
    {
        return $user->hasPermissionTo('delete books');
    }

    public function restore(User $user, Book $book): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Book $book): bool
    {
        return $user->hasRole('admin');
    }
}
