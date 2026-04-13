<?php

namespace Tests\Unit;

use App\Models\Chapter;
use App\Models\ChapterContent;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\UserContentProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;
use Tests\TestCase as BaseTestCase;

class ModelRelationshipsTest extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Test User has many SchoolClasses as teacher
     */
    public function test_user_has_many_school_classes_as_teacher(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $classes = SchoolClass::factory()->count(3)->create(['teacher_id' => $teacher->id]);

        $this->assertCount(3, $teacher->classesAsTeacher);
        $this->assertTrue($teacher->classesAsTeacher->contains($classes[0]));
    }

    /**
     * Test User belongs to many SchoolClasses as student
     */
    public function test_user_has_many_school_classes_as_student(): void
    {
        $student = User::factory()->create(['role' => 'eleve']);
        $teacher = User::factory()->create(['role' => 'prof']);
        $classes = SchoolClass::factory()->count(3)->create(['teacher_id' => $teacher->id]);

        foreach ($classes as $class) {
            $class->students()->attach($student);
        }

        $this->assertCount(3, $student->classes);
        $this->assertTrue($student->classes->contains($classes[0]));
    }

    /**
     * Test SchoolClass belongs to User as teacher
     */
    public function test_school_class_belongs_to_teacher(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

        $this->assertNotNull($class->teacher);
        $this->assertEquals($teacher->id, $class->teacher->id);
    }

    /**
     * Test SchoolClass has many Students
     */
    public function test_school_class_has_many_students(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $students = User::factory()->count(5)->create(['role' => 'eleve']);

        foreach ($students as $student) {
            $class->students()->attach($student);
        }

        $this->assertCount(5, $class->students);
    }

    /**
     * Test SchoolClass has many Courses
     */
    public function test_school_class_has_many_courses(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

        $class->courses()->createMany([
            ['title' => 'Course 1', 'teacher_id' => $teacher->id],
            ['title' => 'Course 2', 'teacher_id' => $teacher->id],
        ]);

        $this->assertCount(2, $class->courses);
    }

    /**
     * Test Course belongs to SchoolClass
     */
    public function test_course_belongs_to_school_class(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);

        $this->assertNotNull($course->schoolClass);
        $this->assertEquals($class->id, $course->schoolClass->id);
    }

    /**
     * Test Course belongs to User as teacher
     */
    public function test_course_belongs_to_teacher(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);

        $this->assertNotNull($course->teacher);
        $this->assertEquals($teacher->id, $course->teacher->id);
    }

    /**
     * Test Course has many Chapters
     */
    public function test_course_has_many_chapters(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);

        $course->chapters()->createMany([
            ['title' => 'Chapter 1', 'position' => 1],
            ['title' => 'Chapter 2', 'position' => 2],
        ]);

        $this->assertCount(2, $course->chapters);
    }

    /**
     * Test Chapter belongs to Course
     */
    public function test_chapter_belongs_to_course(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);
        $chapter = Chapter::factory()->create(['course_id' => $course->id]);

        $this->assertNotNull($chapter->course);
        $this->assertEquals($course->id, $chapter->course->id);
    }

    /**
     * Test Chapter has many ChapterContents
     */
    public function test_chapter_has_many_contents(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);
        $chapter = Chapter::factory()->create(['course_id' => $course->id]);

        $chapter->contents()->createMany([
            ['title' => 'Content 1', 'content' => 'Text 1', 'position' => 1],
            ['title' => 'Content 2', 'content' => 'Text 2', 'position' => 2],
        ]);

        $this->assertCount(2, $chapter->contents);
    }

    /**
     * Test ChapterContent belongs to Chapter
     */
    public function test_chapter_content_belongs_to_chapter(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);
        $chapter = Chapter::factory()->create(['course_id' => $course->id]);
        $content = ChapterContent::factory()->create(['chapter_id' => $chapter->id]);

        $this->assertNotNull($content->chapter);
        $this->assertEquals($chapter->id, $content->chapter->id);
    }

    /**
     * Test ChapterContent has many UserContentProgress records
     */
    public function test_chapter_content_has_many_progress_records(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $student1 = User::factory()->create(['role' => 'eleve']);
        $student2 = User::factory()->create(['role' => 'eleve']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);
        $chapter = Chapter::factory()->create(['course_id' => $course->id]);
        $content = ChapterContent::factory()->create(['chapter_id' => $chapter->id]);

        UserContentProgress::factory()->create([
            'user_id' => $student1->id,
            'chapter_content_id' => $content->id,
        ]);
        UserContentProgress::factory()->create([
            'user_id' => $student2->id,
            'chapter_content_id' => $content->id,
        ]);

        $this->assertCount(2, $content->progressRecords);
    }

    /**
     * Test UserContentProgress belongs to User
     */
    public function test_user_content_progress_belongs_to_user(): void
    {
        $student = User::factory()->create(['role' => 'eleve']);
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);
        $chapter = Chapter::factory()->create(['course_id' => $course->id]);
        $content = ChapterContent::factory()->create(['chapter_id' => $chapter->id]);
        $progress = UserContentProgress::factory()->create([
            'user_id' => $student->id,
            'chapter_content_id' => $content->id,
        ]);

        $this->assertNotNull($progress->user);
        $this->assertEquals($student->id, $progress->user->id);
    }

    /**
     * Test UserContentProgress belongs to ChapterContent
     */
    public function test_user_content_progress_belongs_to_chapter_content(): void
    {
        $student = User::factory()->create(['role' => 'eleve']);
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);
        $chapter = Chapter::factory()->create(['course_id' => $course->id]);
        $content = ChapterContent::factory()->create(['chapter_id' => $chapter->id]);
        $progress = UserContentProgress::factory()->create([
            'user_id' => $student->id,
            'chapter_content_id' => $content->id,
        ]);

        $this->assertNotNull($progress->chapterContent);
        $this->assertEquals($content->id, $progress->chapterContent->id);
    }

    /**
     * Test User has many UserContentProgress records
     */
    public function test_user_has_many_content_progress(): void
    {
        $student = User::factory()->create(['role' => 'eleve']);
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);
        $chapter = Chapter::factory()->create(['course_id' => $course->id]);

        for ($i = 0; $i < 3; $i++) {
            $content = ChapterContent::factory()->create([
                'chapter_id' => $chapter->id,
                'position' => $i + 1,
            ]);
            UserContentProgress::factory()->create([
                'user_id' => $student->id,
                'chapter_content_id' => $content->id,
            ]);
        }

        $this->assertCount(3, $student->contentProgress()->get());
    }
}
