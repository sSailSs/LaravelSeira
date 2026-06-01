<?php

namespace Tests\Feature;

use App\Events\UserEnrolledInClass;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class EnrollmentEventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrolling_student_via_api_dispatches_event(): void
    {
        Event::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'prof']);
        $student = User::factory()->create(['role' => 'eleve']);

        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

        $this->actingAs($admin)
            ->patchJson("/api/school_classes/{$class->id}", [
                'students' => [$student->id],
            ])
            ->assertStatus(200);

        Event::assertDispatched(UserEnrolledInClass::class, function ($event) use ($student, $class) {
            return $event->user->id === $student->id &&
                   $event->class->id === $class->id;
        });
    }

    public function test_re_enrolling_existing_student_does_not_dispatch_event(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'prof']);
        $student = User::factory()->create(['role' => 'eleve']);

        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $class->students()->attach($student);

        Event::fake();

        // Re-enroll same student
        $this->actingAs($admin)
            ->patchJson("/api/school_classes/{$class->id}", [
                'students' => [$student->id],
            ])
            ->assertStatus(200);

        Event::assertNotDispatched(UserEnrolledInClass::class);
    }

    public function test_adding_multiple_students_dispatches_multiple_events(): void
    {
        Event::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'prof']);
        $student1 = User::factory()->create(['role' => 'eleve']);
        $student2 = User::factory()->create(['role' => 'eleve']);
        $student3 = User::factory()->create(['role' => 'eleve']);

        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $class->students()->attach($student1);

        // Add 2 new students
        $this->actingAs($admin)
            ->patchJson("/api/school_classes/{$class->id}", [
                'students' => [$student1->id, $student2->id, $student3->id],
            ])
            ->assertStatus(200);

        Event::assertDispatchedTimes(UserEnrolledInClass::class, 2);
    }
}
