<?php

namespace App\Events;

use App\Models\Course;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Course $course;

    /**
     * Create a new event instance.
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
    }
}
