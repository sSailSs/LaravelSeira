<?php

namespace App\Listeners;

use App\Events\ContentProgressUpdated;
use App\Events\CourseCompleted;
use App\Events\UserEnrolledInClass;
use Illuminate\Support\Facades\Cache;

class UpdateStatisticsListener
{
    /**
     * Handle ContentProgressUpdated event.
     * Track engagement metrics.
     */
    public function handleContentProgressUpdated(ContentProgressUpdated $event): void
    {
        $userId = $event->progress->user_id;
        $courseId = $event->progress->chapterContent->chapter->course_id;

        // Track content views per user
        Cache::increment("user:{$userId}:content_views");

        // Track completion rate per course
        $completionKey = "course:{$courseId}:completion_rate";
        Cache::increment("course:{$courseId}:views");

        if ($event->newStatus === 'completed') {
            Cache::increment("course:{$courseId}:completions");
        }

        // Track engagement per user
        Cache::put("user:{$userId}:last_activity", now(), now()->addDay());
    }

    /**
     * Handle UserEnrolledInClass event.
     * Track enrollment metrics.
     */
    public function handleUserEnrolledInClass(UserEnrolledInClass $event): void
    {
        $classId = $event->class->id;

        // Track enrollments per class
        Cache::increment("class:{$classId}:enrollments");

        // Track total students in class
        Cache::put("class:{$classId}:last_enrollment", now(), now()->addDay());
    }

    /**
     * Handle CourseCompleted event.
     * Track completion achievements.
     */
    public function handleCourseCompleted(CourseCompleted $event): void
    {
        $userId = $event->user->id;
        $courseId = $event->course->id;

        // Mark course as completed
        Cache::put("user:{$userId}:course:{$courseId}:completed", true, now()->addYear());

        // Increment total completions
        Cache::increment("user:{$userId}:courses_completed");
        Cache::increment("course:{$courseId}:student_completions");
    }
}
