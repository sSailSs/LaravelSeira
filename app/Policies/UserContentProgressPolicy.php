<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserContentProgress;

class UserContentProgressPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, UserContentProgress $progress): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($this->isOwnStudentProgress($user, $progress)) {
            return true;
        }

        return $this->canTeacherAccess($user, $progress);
    }

    public function create(User $user, ?UserContentProgress $progress = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isStudent()) {
            return null === $progress || null === $progress->user_id || (int) $progress->user_id === (int) $user->id;
        }

        return null !== $progress && $this->canTeacherAccess($user, $progress);
    }

    public function update(User $user, UserContentProgress $progress): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($this->isOwnStudentProgress($user, $progress)) {
            return true;
        }

        return $this->canTeacherAccess($user, $progress);
    }

    public function delete(User $user, UserContentProgress $progress): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($this->isOwnStudentProgress($user, $progress)) {
            return true;
        }

        return $this->canTeacherAccess($user, $progress);
    }

    private function isOwnStudentProgress(User $user, UserContentProgress $progress): bool
    {
        return $user->isStudent() && (int) $progress->user_id === (int) $user->id;
    }

    private function canTeacherAccess(User $user, UserContentProgress $progress): bool
    {
        return $user->isTeacher() && $this->teacherCanAccessProgress($user, $progress);
    }

    private function teacherCanAccessProgress(User $teacher, UserContentProgress $progress): bool
    {
        $chapterContent = $progress->chapterContent;
        $chapter = $chapterContent?->chapter;
        $course = $chapter?->course;

        return null !== $course && (int) $course->teacher_id === (int) $teacher->id;
    }
}
