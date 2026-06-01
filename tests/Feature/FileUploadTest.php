<?php

namespace Tests\Feature;

use App\Models\FileMetadata;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_teacher_can_upload_file(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $file = UploadedFile::fake()->create('document.pdf', 1024);

        $this->actingAs($teacher)
            ->postJson('/api/file_metadata', [
                'original_name' => 'Test Document',
                'file' => $file,
                'category' => 'pdf',
                'access_level' => 'public',
                'description' => 'Test file',
            ])
            ->assertStatus(201);

        $this->assertDatabaseHas('file_metadata', [
            'uploaded_by' => $teacher->id,
            'category' => 'pdf',
        ]);
    }

    public function test_student_cannot_upload_file(): void
    {
        $student = User::factory()->create(['role' => 'eleve']);
        $file = UploadedFile::fake()->create('document.pdf', 1024);

        $this->actingAs($student)
            ->postJson('/api/file_metadata', [
                'file' => $file,
                'category' => 'pdf',
            ])
            ->assertStatus(403);
    }

    public function test_student_can_view_public_file(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $student = User::factory()->create(['role' => 'eleve']);

        $file = FileMetadata::factory()
            ->public()
            ->create(['uploaded_by' => $teacher->id]);

        $this->actingAs($student)
            ->getJson('/api/file_metadata/' . $file->id)
            ->assertStatus(200)
            ->assertJsonPath('original_name', $file->original_name);
    }

    public function test_student_cannot_view_private_file_of_other_teacher(): void
    {
        $teacher1 = User::factory()->create(['role' => 'prof']);
        $teacher2 = User::factory()->create(['role' => 'prof']);
        $student = User::factory()->create(['role' => 'eleve']);

        $file = FileMetadata::factory()
            ->private()
            ->create(['uploaded_by' => $teacher1->id]);

        $this->actingAs($student)
            ->getJson('/api/file_metadata/' . $file->id)
            ->assertStatus(403);
    }

    public function test_student_can_view_class_file_if_enrolled(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $student = User::factory()->create(['role' => 'eleve']);

        $class->students()->attach($student);

        $file = FileMetadata::factory()
            ->forClass($class)
            ->create(['uploaded_by' => $teacher->id]);

        $this->actingAs($student)
            ->getJson('/api/file_metadata/' . $file->id)
            ->assertStatus(200);
    }

    public function test_student_cannot_view_class_file_if_not_enrolled(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $student = User::factory()->create(['role' => 'eleve']);

        $file = FileMetadata::factory()
            ->forClass($class)
            ->create(['uploaded_by' => $teacher->id]);

        $this->actingAs($student)
            ->getJson('/api/file_metadata/' . $file->id)
            ->assertStatus(403);
    }

    public function test_teacher_can_delete_own_file(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $file = FileMetadata::factory()->create(['uploaded_by' => $teacher->id]);

        $this->actingAs($teacher)
            ->deleteJson('/api/file_metadata/' . $file->id)
            ->assertStatus(204);

        $this->assertDatabaseMissing('file_metadata', ['id' => $file->id]);
    }

    public function test_teacher_cannot_delete_other_teachers_file(): void
    {
        $teacher1 = User::factory()->create(['role' => 'prof']);
        $teacher2 = User::factory()->create(['role' => 'prof']);
        $file = FileMetadata::factory()->create(['uploaded_by' => $teacher1->id]);

        $this->actingAs($teacher2)
            ->deleteJson('/api/file_metadata/' . $file->id)
            ->assertStatus(403);
    }

    public function test_admin_can_view_all_files(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'prof']);

        $files = FileMetadata::factory(3)
            ->private()
            ->create(['uploaded_by' => $teacher->id]);

        $this->actingAs($admin)
            ->getJson('/api/file_metadata')
            ->assertStatus(200)
            ->assertJsonCount(3, 'hydra:member');
    }
}
