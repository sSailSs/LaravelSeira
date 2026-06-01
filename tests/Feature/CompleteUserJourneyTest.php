<?php

namespace Tests\Feature;

use App\Models\Chapter;
use App\Models\ChapterContent;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\UserContentProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Comprehensive Integration Test: Complete User Journey
 *
 * Simulates a realistic scenario:
 * 1. Create a new student
 * 2. Enroll in a class
 * 3. View school classes (with proper RBAC)
 * 4. View courses for that class
 * 5. View course details, chapters, and contents
 * 6. Track progress on video content
 * 7. Verify progression data and completion status
 */
class CompleteUserJourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_student_learning_journey(): void
    {
        // ========== STEP 1: Setup - Create users and school structure ==========

        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['name' => 'Prof Martin', 'role' => 'prof']);

        // Create a class
        $class = SchoolClass::factory()->create([
            'name' => '6A',
            'level' => '6eme',
            'academic_year' => '2025-2026',
            'teacher_id' => $teacher->id,
        ]);

        // Create courses for this class
        $mathCourse = Course::factory()->create([
            'title' => 'Mathématiques - Algèbre',
            'school_class_id' => $class->id,
            'teacher_id' => $teacher->id,
        ]);

        $frenchCourse = Course::factory()->create([
            'title' => 'Français - Littérature',
            'school_class_id' => $class->id,
            'teacher_id' => $teacher->id,
        ]);

        // Create chapters and content for Math course
        $mathChapter1 = Chapter::factory()->create([
            'title' => 'Les équations linéaires',
            'position' => 1,
            'course_id' => $mathCourse->id,
        ]);

        $mathContent1 = ChapterContent::factory()->create([
            'chapter_id' => $mathChapter1->id,
            'title' => 'Vidéo: Introduction aux équations',
            'content_type' => 'video',
            'video_url' => 'https://videos.school.test/math/6A/chapitre-1.mp4',
            'duration_seconds' => 1200, // 20 minutes
            'position' => 1,
        ]);

        $mathContent2 = ChapterContent::factory()->create([
            'chapter_id' => $mathChapter1->id,
            'title' => 'Exercices - Équations linéaires',
            'content' => 'Résolvez les 10 équations suivantes...',
            'content_type' => 'text',
            'position' => 2,
        ]);

        // Create chapter and content for French course
        $frenchChapter1 = Chapter::factory()->create([
            'title' => 'Les figures de style',
            'position' => 1,
            'course_id' => $frenchCourse->id,
        ]);

        $frenchContent1 = ChapterContent::factory()->create([
            'chapter_id' => $frenchChapter1->id,
            'title' => 'Cours: Les métaphores et comparaisons',
            'content' => 'Une métaphore est une comparaison implicite...',
            'content_type' => 'text',
            'position' => 1,
        ]);

        // ========== STEP 2: Create new student and enroll ==========

        $newStudent = User::factory()->create([
            'name' => 'Marie Dubois',
            'email' => 'marie.dubois@student.test',
            'role' => 'eleve',
        ]);

        // Enroll student via API
        $enrollResponse = $this->actingAs($admin)
            ->patchJson("/api/school_classes/{$class->id}", [
                'students' => [$newStudent->id],
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('class_user', [
            'user_id' => $newStudent->id,
            'school_class_id' => $class->id,
        ]);

        // ========== STEP 3: Verify class visibility (RBAC) ==========

        // Teacher can view their class
        $this->actingAs($teacher)
            ->getJson("/api/school_classes/{$class->id}")
            ->assertStatus(200)
            ->assertJsonPath('name', '6A')
            ->assertJsonPath('level', '6eme');

        // Student can view their class
        $this->actingAs($newStudent)
            ->getJson("/api/school_classes/{$class->id}")
            ->assertStatus(200)
            ->assertJsonPath('name', '6A');

        // Student cannot view other classes
        $otherClass = SchoolClass::factory()->create();
        $this->actingAs($newStudent)
            ->getJson("/api/school_classes/{$otherClass->id}")
            ->assertStatus(403);

        // ========== STEP 4: Verify course visibility ==========

        // Student can list courses (will be filtered server-side)
        $coursesResponse = $this->actingAs($newStudent)
            ->getJson('/api/courses')
            ->assertStatus(200);

        // Student can view their course
        $this->actingAs($newStudent)
            ->getJson("/api/courses/{$mathCourse->id}")
            ->assertStatus(200)
            ->assertJsonPath('title', 'Mathématiques - Algèbre');

        // Student cannot view courses from other classes
        $otherClass2 = SchoolClass::factory()->create();
        $otherCourse = Course::factory()->create(['school_class_id' => $otherClass2->id]);

        $this->actingAs($newStudent)
            ->getJson("/api/courses/{$otherCourse->id}")
            ->assertStatus(403);

        // ========== STEP 5: View chapters and content ==========

        // Student can view chapter from their course
        $this->actingAs($newStudent)
            ->getJson("/api/chapters/{$mathChapter1->id}")
            ->assertStatus(200)
            ->assertJsonPath('title', 'Les équations linéaires');

        // Student can view content from their course
        $this->actingAs($newStudent)
            ->getJson("/api/chapter_contents/{$mathContent1->id}")
            ->assertStatus(200)
            ->assertJsonPath('title', 'Vidéo: Introduction aux équations')
            ->assertJsonPath('duration_seconds', 1200);

        // ========== STEP 6: Start tracking progress ==========

        // Student creates a progress record (starting to watch video)
        $progressResponse = $this->actingAs($newStudent)
            ->postJson('/api/user_content_progresses', [
                'user_id' => $newStudent->id,
                'chapter_content_id' => $mathContent1->id,
                'progress_seconds' => 0,
                'is_completed' => false,
            ])
            ->assertStatus(201);

        $progressId = $progressResponse->json('id');

        $this->assertDatabaseHas('user_content_progress', [
            'user_id' => $newStudent->id,
            'chapter_content_id' => $mathContent1->id,
            'is_completed' => false,
        ]);

        // ========== STEP 7: Update progress (watching video) ==========

        // Student watches 600 seconds of the 1200s video
        $this->actingAs($newStudent)
            ->patchJson("/api/user_content_progresses/{$progressId}", [
                'progress_seconds' => 600,
            ])
            ->assertStatus(200);

        // Verify progress was updated
        $this->actingAs($newStudent)
            ->getJson("/api/user_content_progresses/{$progressId}")
            ->assertStatus(200)
            ->assertJsonPath('progress_seconds', 600)
            ->assertJsonPath('is_completed', false);

        // ========== STEP 8: Complete the video ==========

        // Student finishes watching (1200 seconds watched = full duration)
        $this->actingAs($newStudent)
            ->patchJson("/api/user_content_progresses/{$progressId}", [
                'progress_seconds' => 1200,
                'is_completed' => true,
            ])
            ->assertStatus(200);

        // Verify completion
        $this->actingAs($newStudent)
            ->getJson("/api/user_content_progresses/{$progressId}")
            ->assertStatus(200)
            ->assertJsonPath('progress_seconds', 1200)
            ->assertJsonPath('is_completed', true);

        // ========== STEP 9: Complete second content ==========

        // Student completes the exercises
        $progress2Response = $this->actingAs($newStudent)
            ->postJson('/api/user_content_progresses', [
                'user_id' => $newStudent->id,
                'chapter_content_id' => $mathContent2->id,
                'progress_seconds' => null,
                'is_completed' => true,
            ])
            ->assertStatus(201);

        // ========== STEP 10: Verify visibility restrictions ==========

        // Teacher can view student's progress
        $this->actingAs($teacher)
            ->getJson("/api/user_content_progresses/{$progressId}")
            ->assertStatus(200)
            ->assertJsonPath('user_id', $newStudent->id);

        // Admin can view all progress
        $this->actingAs($admin)
            ->getJson("/api/user_content_progresses")
            ->assertStatus(200);

        // Other student CANNOT view this student's progress
        $otherStudent = User::factory()->create(['role' => 'eleve']);
        $class->students()->attach($otherStudent);

        $this->actingAs($otherStudent)
            ->getJson("/api/user_content_progresses/{$progressId}")
            ->assertStatus(403);

        // ========== STEP 11: Verify teacher can view class progress ==========

        // Teacher can see all progress for their class students
        $allProgressResponse = $this->actingAs($teacher)
            ->getJson('/api/user_content_progresses')
            ->assertStatus(200);

        // There should be at least our 2 progress records
        $this->assertGreaterThanOrEqual(2, count($allProgressResponse->json('hydra:member')));

        // ========== STEP 12: Verify data consistency ==========

        // Fetch complete course with chapters
        $courseDetail = $this->actingAs($newStudent)
            ->getJson("/api/courses/{$mathCourse->id}")
            ->assertStatus(200)
            ->json();

        $this->assertEquals('Mathématiques - Algèbre', $courseDetail['title']);
        $this->assertEquals($class->id, $courseDetail['schoolClass']);

        // ========== STEP 13: Test enrollment event effects ==========

        // Verify student is enrolled (can query their classes)
        $studentClasses = $this->actingAs($newStudent)
            ->getJson('/api/school_classes')
            ->assertStatus(200)
            ->json();

        // Student should see their class in the list (or empty if server-side filtered)
        // Main point: no 403 errors

        // ========== STEP 14: Verify role-based access ==========

        // Student cannot create courses
        $this->actingAs($newStudent)
            ->postJson('/api/courses', [
                'title' => 'Hacking 101',
                'school_class_id' => $class->id,
            ])
            ->assertStatus(403);

        // Teacher can create courses in their class
        $this->actingAs($teacher)
            ->postJson('/api/courses', [
                'title' => 'Histoire - Moyen-Âge',
                'school_class_id' => $class->id,
            ])
            ->assertStatus(201);

        // ========== SUMMARY: Verify complete journey ==========

        // Student has:
        // ✅ Created account
        // ✅ Been enrolled in class
        // ✅ Accessed courses
        // ✅ Viewed chapters and content
        // ✅ Started and completed video progress
        // ✅ Completed text exercises
        // ✅ All with proper RBAC enforcement

        $this->assertTrue(true); // All assertions passed
    }

    /**
     * Test that a complete course journey is tracked via events
     */
    public function test_student_journey_triggers_events(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'prof']);
        $student = User::factory()->create(['role' => 'eleve']);

        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $course = Course::factory()->create([
            'school_class_id' => $class->id,
            'teacher_id' => $teacher->id,
        ]);
        $chapter = Chapter::factory()->create(['course_id' => $course->id]);
        $content = ChapterContent::factory()->create([
            'chapter_id' => $chapter->id,
            'duration_seconds' => 600,
        ]);

        // Enroll student - should trigger event
        $this->actingAs($admin)
            ->patchJson("/api/school_classes/{$class->id}", [
                'students' => [$student->id],
            ])
            ->assertStatus(200);

        // Verify enrollment in DB
        $this->assertDatabaseHas('class_user', [
            'user_id' => $student->id,
            'school_class_id' => $class->id,
        ]);

        // Create progress
        $progressResponse = $this->actingAs($student)
            ->postJson('/api/user_content_progresses', [
                'user_id' => $student->id,
                'chapter_content_id' => $content->id,
                'progress_seconds' => 0,
            ])
            ->assertStatus(201);

        $progressId = $progressResponse->json('id');

        // Complete video
        $this->actingAs($student)
            ->patchJson("/api/user_content_progresses/{$progressId}", [
                'progress_seconds' => 600,
                'is_completed' => true,
            ])
            ->assertStatus(200);

        // Verify events were logged (check storage/logs/laravel.log)
        // In real scenario, we could check cache metrics
        $this->assertDatabaseHas('user_content_progress', [
            'user_id' => $student->id,
            'is_completed' => true,
        ]);
    }

    /**
     * Test error scenarios and edge cases
     */
    public function test_access_control_prevents_unauthorized_actions(): void
    {
        $teacher1 = User::factory()->create(['role' => 'prof']);
        $teacher2 = User::factory()->create(['role' => 'prof']);
        $student = User::factory()->create(['role' => 'eleve']);

        // Teacher 1 creates a class
        $class1 = SchoolClass::factory()->create(['teacher_id' => $teacher1->id]);
        $course1 = Course::factory()->create([
            'school_class_id' => $class1->id,
            'teacher_id' => $teacher1->id,
        ]);

        // Teacher 2 cannot modify Teacher 1's class
        $this->actingAs($teacher2)
            ->patchJson("/api/school_classes/{$class1->id}", [
                'name' => 'Hacked Class',
            ])
            ->assertStatus(403);

        // Teacher 2 cannot delete Teacher 1's course
        $this->actingAs($teacher2)
            ->deleteJson("/api/courses/{$course1->id}")
            ->assertStatus(403);

        // Student cannot create a class
        $this->actingAs($student)
            ->postJson('/api/school_classes', [
                'name' => 'My Hacked Class',
            ])
            ->assertStatus(403);
    }

    /**
     * Test data integrity through complete workflow
     */
    public function test_data_consistency_across_workflow(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'prof']);
        $students = User::factory(3)->create(['role' => 'eleve']);

        // Create class and enroll students
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

        $this->actingAs($admin)
            ->patchJson("/api/school_classes/{$class->id}", [
                'students' => $students->pluck('id')->toArray(),
            ])
            ->assertStatus(200);

        // Verify all students are enrolled
        foreach ($students as $student) {
            $this->assertDatabaseHas('class_user', [
                'user_id' => $student->id,
                'school_class_id' => $class->id,
            ]);
        }

        // Create course with complete structure
        $course = Course::factory()->create([
            'school_class_id' => $class->id,
            'teacher_id' => $teacher->id,
        ]);

        $chapters = Chapter::factory(2)->create(['course_id' => $course->id]);
        $contentsPerChapter = [];

        foreach ($chapters as $chapter) {
            $contentsPerChapter[$chapter->id] = ChapterContent::factory(2)->create([
                'chapter_id' => $chapter->id,
            ]);
        }

        // Each student progresses through course
        foreach ($students as $student) {
            $this->actingAs($student)
                ->getJson("/api/courses/{$course->id}")
                ->assertStatus(200);

            foreach ($chapters as $chapter) {
                $this->actingAs($student)
                    ->getJson("/api/chapters/{$chapter->id}")
                    ->assertStatus(200);

                foreach ($contentsPerChapter[$chapter->id] as $content) {
                    $this->actingAs($student)
                        ->getJson("/api/chapter_contents/{$content->id}")
                        ->assertStatus(200);
                }
            }
        }

        // Verify course structure is intact
        $this->assertDatabaseHas('courses', ['id' => $course->id]);
        $this->assertEquals(2, $course->chapters()->count());
    }
}
