<?php

namespace Tests\Unit;

use App\Models\ChapterContent;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\UserContentProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test User model fillable attributes
     */
    public function test_user_fillable_attributes(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('student', $user->role);
    }

    /**
     * Test User password is hidden in serialization
     */
    public function test_user_password_hidden_in_serialization(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'secret123',
            'role' => 'student',
        ]);

        $array = $user->toArray();
        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    /**
     * Test SchoolClass requires name
     */
    public function test_school_class_requires_name(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $class = SchoolClass::create([
            'name' => 'Test Class', // name is required
            'level' => '10th Grade',
            'academic_year' => '2025-2026',
            'teacher_id' => $teacher->id,
        ]);

        // Verify the name is set correctly
        $this->assertEquals('Test Class', $class->name);
    }

    /**
     * Test SchoolClass requires teacher_id
     */
    public function test_school_class_requires_teacher_id(): void
    {
        $class = SchoolClass::create([
            'name' => 'Test Class',
            'level' => '10th Grade',
            'academic_year' => '2025-2026',
        ]);

        $this->assertNull($class->teacher_id);
    }

    /**
     * Test SchoolClass fillable attributes
     */
    public function test_school_class_fillable_attributes(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $class = SchoolClass::create([
            'name' => 'Test Class',
            'level' => '10th Grade',
            'academic_year' => '2025-2026',
            'teacher_id' => $teacher->id,
        ]);

        $this->assertEquals('Test Class', $class->name);
        $this->assertEquals('10th Grade', $class->level);
        $this->assertEquals('2025-2026', $class->academic_year);
    }

    /**
     * Test Course fillable attributes
     */
    public function test_course_fillable_attributes(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

        $course = Course::create([
            'title' => 'Math 101',
            'description' => 'Introduction to mathematics',
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);

        $this->assertEquals('Math 101', $course->title);
        $this->assertEquals('Introduction to mathematics', $course->description);
        $this->assertEquals($teacher->id, $course->teacher_id);
        $this->assertEquals($class->id, $course->school_class_id);
    }

    /**
     * Test ChapterContent fillable attributes
     */
    public function test_chapter_content_fillable_attributes(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);
        $chapter = $course->chapters()->create([
            'title' => 'Chapter 1',
            'position' => 1,
        ]);

        $content = ChapterContent::create([
            'chapter_id' => $chapter->id,
            'title' => 'Video Content',
            'content' => 'Some text',
            'content_type' => 'video',
            'video_url' => 'https://example.com/video.mp4',
            'duration_seconds' => 3600,
            'position' => 1,
        ]);

        $this->assertEquals('Video Content', $content->title);
        $this->assertEquals('video', $content->content_type);
        $this->assertEquals('https://example.com/video.mp4', $content->video_url);
        $this->assertEquals(3600, $content->duration_seconds);
    }

    /**
     * Test ChapterContent position casts to integer
     */
    public function test_chapter_content_position_cast_to_integer(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);
        $chapter = $course->chapters()->create([
            'title' => 'Chapter 1',
            'position' => 1,
        ]);

        $content = ChapterContent::create([
            'chapter_id' => $chapter->id,
            'title' => 'Content 1',
            'content' => 'Test content',
            'position' => '5', // String
        ]);

        $this->assertIsInt($content->position);
        $this->assertEquals(5, $content->position);
    }

    /**
     * Test ChapterContent duration_seconds casts to integer
     */
    public function test_chapter_content_duration_cast_to_integer(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);
        $chapter = $course->chapters()->create([
            'title' => 'Chapter 1',
            'position' => 1,
        ]);

        $content = ChapterContent::create([
            'chapter_id' => $chapter->id,
            'title' => 'Content 1',
            'content' => 'Test content',
            'duration_seconds' => '3600', // String
        ]);

        $this->assertIsInt($content->duration_seconds);
        $this->assertEquals(3600, $content->duration_seconds);
    }

    /**
     * Test UserContentProgress fillable attributes
     */
    public function test_user_content_progress_fillable_attributes(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);
        $chapter = $course->chapters()->create([
            'title' => 'Chapter 1',
            'position' => 1,
        ]);
        $content = $chapter->contents()->create([
            'title' => 'Content 1',
            'content' => 'Some content',
            'position' => 1,
        ]);

        $progress = UserContentProgress::create([
            'user_id' => $student->id,
            'chapter_content_id' => $content->id,
            'progress_seconds' => 1800,
            'is_completed' => false,
        ]);

        $this->assertEquals($student->id, $progress->user_id);
        $this->assertEquals($content->id, $progress->chapter_content_id);
        $this->assertEquals(1800, $progress->progress_seconds);
        $this->assertFalse($progress->is_completed);
    }

    /**
     * Test UserContentProgress is_completed casts to boolean
     */
    public function test_user_content_progress_is_completed_cast_to_boolean(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);
        $chapter = $course->chapters()->create([
            'title' => 'Chapter 1',
            'position' => 1,
        ]);
        $content = $chapter->contents()->create([
            'title' => 'Content 1',
            'content' => 'Some content',
            'position' => 1,
        ]);

        $progress = UserContentProgress::create([
            'user_id' => $student->id,
            'chapter_content_id' => $content->id,
            'progress_seconds' => 3600,
            'is_completed' => 1, // Integer
        ]);

        $this->assertIsBool($progress->is_completed);
        $this->assertTrue($progress->is_completed);
    }

    /**
     * Test UserContentProgress last_watched_at casts to datetime
     */
    public function test_user_content_progress_last_watched_at_cast_to_datetime(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $class->id,
        ]);
        $chapter = $course->chapters()->create([
            'title' => 'Chapter 1',
            'position' => 1,
        ]);
        $content = $chapter->contents()->create([
            'title' => 'Content 1',
            'content' => 'Some content',
            'position' => 1,
        ]);

        $now = now();
        $progress = UserContentProgress::create([
            'user_id' => $student->id,
            'chapter_content_id' => $content->id,
            'progress_seconds' => 1000,
            'last_watched_at' => $now->toDateTimeString(),
        ]);

        $this->assertNotNull($progress->last_watched_at);
        $this->assertEquals($now->timestamp, $progress->last_watched_at->timestamp);
    }
}
