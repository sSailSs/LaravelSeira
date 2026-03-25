<?php

namespace App\Policies;

use App\Models\Chapter;
use App\Models\User;

class ChapterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function view(User $user, Chapter $chapter): bool
    {
        $teacherId = $this->resolveChapterTeacherId($chapter);
        if (null === $teacherId) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $this->isTeacherOwner($user, $teacherId);
        }

        if ($user->isStudent()) {
            return $this->isStudentEnrolledForChapter($user, $chapter);
        }

        return false;
    }

    public function create(User $user, ?Chapter $chapter = null): bool
    {
        return $this->canManageChapter($user, $chapter);
    }

    public function update(User $user, Chapter $chapter): bool
    {
        return $this->canManageChapter($user, $chapter);
    }

    public function delete(User $user, Chapter $chapter): bool
    {
        return $this->canManageChapter($user, $chapter);
    }

    private function canManageChapter(User $user, ?Chapter $chapter = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->isTeacher()) {
            return false;
        }

        if (null === $chapter || null === $chapter->course_id) {
            return true;
        }

        $teacherId = $this->resolveChapterTeacherId($chapter);

        return null !== $teacherId && $this->isTeacherOwner($user, $teacherId);
    }

    private function resolveChapterTeacherId(Chapter $chapter): ?int
    {
        $course = $chapter->course;

        return null === $course || null === $course->teacher_id
            ? null
            : (int) $course->teacher_id;
    }

    private function isTeacherOwner(User $user, int $teacherId): bool
    {
        return (int) $user->id === $teacherId;
    }

    private function isStudentEnrolledForChapter(User $user, Chapter $chapter): bool
    {
        $course = $chapter->course;

        return null !== $course
            && $course->schoolClass()->exists()
            && $course->schoolClass->students()->whereKey($user->id)->exists();
    }
}
