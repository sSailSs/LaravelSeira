<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canRead($user);
    }

    public function view(User $user, Book $book): bool
    {
        return $this->canRead($user);
    }

    public function create(User $user, ?Book $book = null): bool
    {
        return $this->canWrite($user);
    }

    public function update(User $user, Book $book): bool
    {
        return $this->canWrite($user);
    }

    public function delete(User $user, Book $book): bool
    {
        return $this->canWrite($user);
    }

    private function canRead(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher() || $user->isStudent();
    }

    private function canWrite(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }
}
