<?php

namespace App\Listeners;

use App\Events\CourseCreated;
use App\Events\UserEnrolledInClass;

class SendNotificationListener
{
    /**
     * Handle UserEnrolledInClass event.
     * Could send email/in-app notification to student.
     */
    public function handleUserEnrolledInClass(UserEnrolledInClass $event): void
    {
        // Example: Send welcome notification
        // Notification::send($event->user, new EnrolledInClassNotification($event->class));

        // For now, just log (in production would send real notifications)
        activity()
            ->causedBy($event->user)
            ->log("Enrolled in class: {$event->class->name}");
    }

    /**
     * Handle CourseCreated event.
     * Notify teacher that course was created.
     */
    public function handleCourseCreated(CourseCreated $event): void
    {
        // Example: Send notification to teacher
        // Notification::send($event->course->teacher, new CourseCreatedNotification($event->course));

        activity()
            ->causedBy($event->course->teacher)
            ->log("Course created: {$event->course->title}");
    }
}
