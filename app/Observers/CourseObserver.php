<?php

namespace App\Observers;

use App\Events\CourseCreated;
use App\Models\Course;

class CourseObserver
{
    /**
     * Handle the Course "created" event.
     */
    public function created(Course $course): void
    {
        CourseCreated::dispatch($course);
    }
}
