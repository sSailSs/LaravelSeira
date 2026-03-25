<?php

namespace Tests\Unit;

use App\Models\Chapter;
use App\Models\ChapterContent;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\UserContentProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class RbacPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_courses(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);

        $this->assertTrue(Gate::forUser($admin)->allows('viewAny', Course::class));
        $this->assertTrue(Gate::forUser($admin)->allows('view', $course));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $course));
        $this->assertTrue(Gate::forUser($admin)->allows('delete', $course));
    }

    public function test_teacher_can_manage_own_course_only(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $otherTeacher = User::factory()->create(['role' => 'prof']);

        $ownClass = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $otherClass = SchoolClass::factory()->create(['teacher_id' => $otherTeacher->id]);

        $ownCourse = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $ownClass->id,
        ]);

        $otherCourse = Course::factory()->create([
            'teacher_id' => $otherTeacher->id,
            'school_class_id' => $otherClass->id,
        ]);

        $this->assertTrue(Gate::forUser($teacher)->allows('view', $ownCourse));
        $this->assertTrue(Gate::forUser($teacher)->allows('update', $ownCourse));
        $this->assertTrue(Gate::forUser($teacher)->allows('delete', $ownCourse));

        $this->assertFalse(Gate::forUser($teacher)->allows('view', $otherCourse));
        $this->assertFalse(Gate::forUser($teacher)->allows('update', $otherCourse));
        $this->assertFalse(Gate::forUser($teacher)->allows('delete', $otherCourse));
    }

    public function test_student_can_view_only_enrolled_course(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $student = User::factory()->create(['role' => 'eleve']);

        $classA = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $classB = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

        $classA->students()->attach($student->id);

        $enrolledCourse = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $classA->id,
        ]);

        $notEnrolledCourse = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $classB->id,
        ]);

        $this->assertFalse(Gate::forUser($student)->allows('viewAny', Course::class));
        $this->assertTrue(Gate::forUser($student)->allows('view', $enrolledCourse));
        $this->assertFalse(Gate::forUser($student)->allows('view', $notEnrolledCourse));
        $this->assertFalse(Gate::forUser($student)->allows('update', $enrolledCourse));
    }

    public function test_user_policy_allows_self_update_but_not_self_delete(): void
    {
        $student = User::factory()->create(['role' => 'eleve']);

        $this->assertTrue(Gate::forUser($student)->allows('view', $student));
        $this->assertTrue(Gate::forUser($student)->allows('update', $student));
        $this->assertFalse(Gate::forUser($student)->allows('delete', $student));
    }

    public function test_teacher_can_create_class_for_self_only(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);

        $ownClassDraft = new SchoolClass([
            'name' => '3eme A',
            'level' => '3eme',
            'academic_year' => '2025-2026',
            'teacher_id' => $teacher->id,
        ]);

        $otherClassDraft = new SchoolClass([
            'name' => '3eme B',
            'level' => '3eme',
            'academic_year' => '2025-2026',
            'teacher_id' => $teacher->id + 999,
        ]);

        $this->assertTrue(Gate::forUser($teacher)->allows('create', $ownClassDraft));
        $this->assertFalse(Gate::forUser($teacher)->allows('create', $otherClassDraft));
    }

    public function test_progress_policy_restricts_to_owner_or_teacher_of_course(): void
    {
        $teacherA = User::factory()->create(['role' => 'prof']);
        $teacherB = User::factory()->create(['role' => 'prof']);
        $student = User::factory()->create(['role' => 'eleve']);
        $otherStudent = User::factory()->create(['role' => 'eleve']);

        $classA = SchoolClass::factory()->create(['teacher_id' => $teacherA->id]);
        $classA->students()->attach([$student->id, $otherStudent->id]);

        $classB = SchoolClass::factory()->create(['teacher_id' => $teacherB->id]);

        $courseA = Course::factory()->create([
            'teacher_id' => $teacherA->id,
            'school_class_id' => $classA->id,
        ]);

        $courseB = Course::factory()->create([
            'teacher_id' => $teacherB->id,
            'school_class_id' => $classB->id,
        ]);

        $chapterA = Chapter::factory()->create(['course_id' => $courseA->id]);
        $chapterB = Chapter::factory()->create(['course_id' => $courseB->id]);

        $contentA = ChapterContent::factory()->create(['chapter_id' => $chapterA->id]);
        $contentB = ChapterContent::factory()->create(['chapter_id' => $chapterB->id]);

        $studentProgressA = UserContentProgress::factory()->create([
            'user_id' => $student->id,
            'chapter_content_id' => $contentA->id,
        ]);

        $otherStudentProgressA = UserContentProgress::factory()->create([
            'user_id' => $otherStudent->id,
            'chapter_content_id' => $contentA->id,
        ]);

        $studentProgressB = UserContentProgress::factory()->create([
            'user_id' => $student->id,
            'chapter_content_id' => $contentB->id,
        ]);

        $this->assertTrue(Gate::forUser($student)->allows('view', $studentProgressA));
        $this->assertFalse(Gate::forUser($student)->allows('view', $otherStudentProgressA));
        $this->assertTrue(Gate::forUser($teacherA)->allows('view', $studentProgressA));
        $this->assertFalse(Gate::forUser($teacherA)->allows('view', $studentProgressB));
        $this->assertFalse(Gate::forUser($teacherB)->allows('view', $studentProgressA));

        $this->assertTrue(Gate::forUser($student)->allows('update', $studentProgressA));
        $this->assertFalse(Gate::forUser($student)->allows('update', $otherStudentProgressA));
    }
}
