<?php

namespace App\Events;

use App\Models\Course;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;

    public Course $course;

    public int $completionPercentage;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, Course $course, int $completionPercentage = 100)
    {
        $this->user = $user;
        $this->course = $course;
        $this->completionPercentage = $completionPercentage;
    }
}
