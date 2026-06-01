<?php

namespace Tests\Feature;

use App\Events\ContentProgressUpdated;
use App\Events\CourseCompleted;
use App\Events\CourseCreated;
use App\Events\UserEnrolledInClass;
use App\Models\Chapter;
use App\Models\ChapterContent;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\UserContentProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class EventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_progress_created_dispatches_event(): void
    {
        Event::fake();

        $student = User::factory()->create(['role' => 'eleve']);
        $course = Course::factory()->create();
        $chapter = Chapter::factory()->create(['course_id' => $course->id]);
        $content = ChapterContent::factory()->create(['chapter_id' => $chapter->id]);

        UserContentProgress::create([
            'user_id' => $student->id,
            'chapter_content_id' => $content->id,
            'progress_seconds' => 0,
            'is_completed' => false,
        ]);

        Event::assertDispatched(ContentProgressUpdated::class, function ($event) {
            return $event->newStatus === 'in_progress' &&
                   $event->previousStatus === 'not_started';
        });
    }

    public function test_content_progress_completion_dispatches_event(): void
    {
        Event::fake();

        $student = User::factory()->create(['role' => 'eleve']);
        $course = Course::factory()->create();
        $chapter = Chapter::factory()->create(['course_id' => $course->id]);
        $content = ChapterContent::factory()->create(['chapter_id' => $chapter->id, 'duration_seconds' => 600]);

        $progress = UserContentProgress::create([
            'user_id' => $student->id,
            'chapter_content_id' => $content->id,
            'progress_seconds' => 300,
            'is_completed' => false,
        ]);

        Event::resetExpectations();
        Event::fake();

        // Update to completed
        $progress->update([
            'progress_seconds' => 600,
            'is_completed' => true,
        ]);

        Event::assertDispatched(ContentProgressUpdated::class, function ($event) {
            return $event->newStatus === 'completed' &&
                   $event->previousStatus === 'in_progress';
        });
    }

    public function test_course_created_dispatches_event(): void
    {
        Event::fake();

        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

        Course::create([
            'title' => 'New Course',
            'school_class_id' => $class->id,
            'teacher_id' => $teacher->id,
        ]);

        Event::assertDispatched(CourseCreated::class);
    }

    public function test_course_created_event_contains_course_data(): void
    {
        Event::fake();

        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

        $course = Course::create([
            'title' => 'Physics 101',
            'description' => 'Introduction to Physics',
            'school_class_id' => $class->id,
            'teacher_id' => $teacher->id,
        ]);

        Event::assertDispatched(CourseCreated::class, function ($event) use ($course) {
            return $event->course->id === $course->id &&
                   $event->course->title === 'Physics 101';
        });
    }

    public function test_user_enrolled_event_can_be_manually_dispatched(): void
    {
        Event::fake();

        $student = User::factory()->create(['role' => 'eleve']);
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

        UserEnrolledInClass::dispatch($student, $class);

        Event::assertDispatched(UserEnrolledInClass::class, function ($event) use ($student, $class) {
            return $event->user->id === $student->id &&
                   $event->class->id === $class->id;
        });
    }

    public function test_course_completed_event_can_be_manually_dispatched(): void
    {
        Event::fake();

        $student = User::factory()->create(['role' => 'eleve']);
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create(['school_class_id' => $class->id, 'teacher_id' => $teacher->id]);

        CourseCompleted::dispatch($student, $course, 100);

        Event::assertDispatched(CourseCompleted::class, function ($event) use ($student, $course) {
            return $event->user->id === $student->id &&
                   $event->course->id === $course->id &&
                   $event->completionPercentage === 100;
        });
    }
}
