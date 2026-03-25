<?php

namespace Tests\Feature;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_post_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'dup@example.com']);
        $this->actingAs($this->createAdmin());

        $response = $this->postJson('/api/users', [
            'name' => 'Duplicate',
            'email' => 'dup@example.com',
            'password' => 'password123',
            'role' => 'eleve',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_post_rejects_invalid_email_format(): void
    {
        $this->actingAs($this->createAdmin());

        $response = $this->postJson('/api/users', [
            'name' => 'Bad Email',
            'email' => 'bad-email',
            'password' => 'password123',
            'role' => 'eleve',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_post_rejects_invalid_role_value(): void
    {
        $this->actingAs($this->createAdmin());

        $response = $this->postJson('/api/users', [
            'name' => 'Wrong Role',
            'email' => 'wrong-role@example.com',
            'password' => 'password123',
            'role' => 'manager',
        ]);

        $response->assertStatus(422);
    }

    public function test_chapter_content_post_rejects_unsupported_content_type(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $schoolClass = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $schoolClass->id,
        ]);
        $chapter = Chapter::factory()->create(['course_id' => $course->id]);

        $this->actingAs($this->createAdmin());

        $response = $this->postJson('/api/chapter_contents', [
            'chapter_id' => $chapter->id,
            'title' => 'Unsupported Type',
            'content' => 'Quiz content',
            'content_type' => 'quiz',
        ]);

        $response->assertStatus(422);
    }

    public function test_chapter_content_post_requires_video_fields_for_video_type(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $schoolClass = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $schoolClass->id,
        ]);
        $chapter = Chapter::factory()->create(['course_id' => $course->id]);

        $this->actingAs($this->createAdmin());

        $response = $this->postJson('/api/chapter_contents', [
            'chapter_id' => $chapter->id,
            'title' => 'Video lesson',
            'content' => 'Watch this',
            'content_type' => 'video',
            'duration_seconds' => 120,
        ]);

        $response->assertStatus(422);
    }

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'password' => 'password123',
        ]);
    }
}
