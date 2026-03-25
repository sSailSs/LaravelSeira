<?php

namespace App\Policies;

use App\Models\SchoolClass;
use App\Models\User;

class SchoolClassPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function view(User $user, SchoolClass $schoolClass): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $this->isClassTeacher($user, $schoolClass);
        }

        if ($user->isStudent()) {
            return $schoolClass->students()->whereKey($user->id)->exists();
        }

        return false;
    }

    public function create(User $user, ?SchoolClass $schoolClass = null): bool
    {
        return $this->canManageClass($user, $schoolClass);
    }

    public function update(User $user, SchoolClass $schoolClass): bool
    {
        return $this->canManageClass($user, $schoolClass);
    }

    public function delete(User $user, SchoolClass $schoolClass): bool
    {
        return $this->canManageClass($user, $schoolClass);
    }

    private function canManageClass(User $user, ?SchoolClass $schoolClass = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->isTeacher()) {
            return false;
        }

        if (null === $schoolClass || null === $schoolClass->teacher_id) {
            return true;
        }

        return $this->isClassTeacher($user, $schoolClass);
    }

    private function isClassTeacher(User $user, SchoolClass $schoolClass): bool
    {
        return (int) $schoolClass->teacher_id === (int) $user->id;
    }
}
