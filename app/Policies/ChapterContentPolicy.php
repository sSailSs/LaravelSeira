<?php

namespace App\Policies;

use App\Models\ChapterContent;
use App\Models\User;

class ChapterContentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function view(User $user, ChapterContent $chapterContent): bool
    {
        $teacherId = $this->resolveTeacherId($chapterContent);
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
            return $this->isStudentEnrolledForContent($user, $chapterContent);
        }

        return false;
    }

    public function create(User $user, ?ChapterContent $chapterContent = null): bool
    {
        return $this->canManageContent($user, $chapterContent);
    }

    public function update(User $user, ChapterContent $chapterContent): bool
    {
        return $this->canManageContent($user, $chapterContent);
    }

    public function delete(User $user, ChapterContent $chapterContent): bool
    {
        return $this->canManageContent($user, $chapterContent);
    }

    private function canManageContent(User $user, ?ChapterContent $chapterContent = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->isTeacher()) {
            return false;
        }

        if (null === $chapterContent || null === $chapterContent->chapter_id) {
            return true;
        }

        $teacherId = $this->resolveTeacherId($chapterContent);

        return null !== $teacherId && $this->isTeacherOwner($user, $teacherId);
    }

    private function resolveTeacherId(ChapterContent $chapterContent): ?int
    {
        $chapter = $chapterContent->chapter;
        $course = $chapter?->course;

        return null === $course || null === $course->teacher_id
            ? null
            : (int) $course->teacher_id;
    }

    private function isTeacherOwner(User $user, int $teacherId): bool
    {
        return (int) $user->id === $teacherId;
    }

    private function isStudentEnrolledForContent(User $user, ChapterContent $chapterContent): bool
    {
        $chapter = $chapterContent->chapter;
        $course = $chapter?->course;

        return null !== $course
            && $course->schoolClass()->exists()
            && $course->schoolClass->students()->whereKey($user->id)->exists();
    }
}
