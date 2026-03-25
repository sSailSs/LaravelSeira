<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function view(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $this->isCourseTeacher($user, $course);
        }

        if ($user->isStudent()) {
            return $this->isStudentInCourseClass($user, $course);
        }

        return false;
    }

    public function create(User $user, ?Course $course = null): bool
    {
        return $this->canManageCourse($user, $course);
    }

    public function update(User $user, Course $course): bool
    {
        return $this->canManageCourse($user, $course);
    }

    public function delete(User $user, Course $course): bool
    {
        return $this->canManageCourse($user, $course);
    }

    private function canManageCourse(User $user, ?Course $course = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->isTeacher()) {
            return false;
        }

        if (null === $course || null === $course->teacher_id) {
            return true;
        }

        return $this->isCourseTeacher($user, $course);
    }

    private function isCourseTeacher(User $user, Course $course): bool
    {
        return (int) $course->teacher_id === (int) $user->id;
    }

    private function isStudentInCourseClass(User $user, Course $course): bool
    {
        return $course->schoolClass()->exists()
            && $course->schoolClass->students()->whereKey($user->id)->exists();
    }
}
