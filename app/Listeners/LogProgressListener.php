<?php

namespace App\Listeners;

use App\Events\ContentProgressUpdated;
use App\Events\CourseCompleted;
use App\Events\CourseCreated;
use App\Events\UserEnrolledInClass;
use Illuminate\Support\Facades\Log;

class LogProgressListener
{
    /**
     * Handle ContentProgressUpdated event.
     */
    public function handleContentProgressUpdated(ContentProgressUpdated $event): void
    {
        Log::info('Content progress updated', [
            'user_id' => $event->progress->user_id,
            'chapter_content_id' => $event->progress->chapter_content_id,
            'previous_status' => $event->previousStatus,
            'new_status' => $event->newStatus,
            'progress_seconds' => $event->progress->progress_seconds,
            'is_completed' => $event->progress->is_completed,
        ]);
    }

    /**
     * Handle UserEnrolledInClass event.
     */
    public function handleUserEnrolledInClass(UserEnrolledInClass $event): void
    {
        Log::info('User enrolled in class', [
            'user_id' => $event->user->id,
            'user_name' => $event->user->name,
            'class_id' => $event->class->id,
            'class_name' => $event->class->name,
            'role' => $event->user->role,
        ]);
    }

    /**
     * Handle CourseCreated event.
     */
    public function handleCourseCreated(CourseCreated $event): void
    {
        Log::info('Course created', [
            'course_id' => $event->course->id,
            'title' => $event->course->title,
            'teacher_id' => $event->course->teacher_id,
            'school_class_id' => $event->course->school_class_id,
        ]);
    }

    /**
     * Handle CourseCompleted event.
     */
    public function handleCourseCompleted(CourseCompleted $event): void
    {
        Log::info('Course completed', [
            'user_id' => $event->user->id,
            'course_id' => $event->course->id,
            'completion_percentage' => $event->completionPercentage,
        ]);
    }
}
