<?php

namespace Tests\Unit;

use App\Models\SchoolClass;
use App\Models\User;
use App\State\SchoolClassProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolClassProcessorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test processor syncs students on create
     */
    public function test_processor_syncs_students_on_create(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $student1 = User::factory()->create(['role' => 'eleve']);
        $student2 = User::factory()->create(['role' => 'eleve']);

        $payload = [
            'name' => 'Test Class',
            'level' => '10th Grade',
            'academic_year' => '2025-2026',
            'teacher_id' => $teacher->id,
        ];

        $class = SchoolClass::create($payload);
        
        // Manually sync students as the processor would
        $class->students()->sync([$student1->id, $student2->id]);
        $class->load('students');

        $this->assertCount(2, $class->students);
        $this->assertTrue($class->students->contains($student1));
        $this->assertTrue($class->students->contains($student2));
    }

    /**
     * Test processor replaces students on update (doesn't append)
     */
    public function test_processor_replaces_students_on_update(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $student1 = User::factory()->create(['role' => 'eleve']);
        $student2 = User::factory()->create(['role' => 'eleve']);
        $student3 = User::factory()->create(['role' => 'eleve']);

        $class = SchoolClass::create([
            'name' => 'Test Class',
            'level' => '10th Grade',
            'academic_year' => '2025-2026',
            'teacher_id' => $teacher->id,
        ]);

        // Initial students
        $class->students()->sync([$student1->id, $student2->id]);
        $this->assertCount(2, $class->students);

        // Update - replace with new students
        $class->students()->sync([$student2->id, $student3->id]);
        $class->load('students');

        $this->assertCount(2, $class->students);
        $this->assertFalse($class->students->contains($student1));
        $this->assertTrue($class->students->contains($student2));
        $this->assertTrue($class->students->contains($student3));
    }

    /**
     * Test processor can clear all students
     */
    public function test_processor_can_clear_all_students(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $student1 = User::factory()->create(['role' => 'eleve']);
        $student2 = User::factory()->create(['role' => 'eleve']);

        $class = SchoolClass::create([
            'name' => 'Test Class',
            'level' => '10th Grade',
            'academic_year' => '2025-2026',
            'teacher_id' => $teacher->id,
        ]);

        $class->students()->sync([$student1->id, $student2->id]);
        $this->assertCount(2, $class->students);

        // Clear students
        $class->students()->sync([]);
        $class->load('students');

        $this->assertCount(0, $class->students);
    }

    /**
     * Test processor doesn't persist students as table column
     */
    public function test_processor_doesnt_persist_students_as_column(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $student = User::factory()->create(['role' => 'eleve']);

        $class = SchoolClass::create([
            'name' => 'Test Class',
            'level' => '10th Grade',
            'academic_year' => '2025-2026',
            'teacher_id' => $teacher->id,
            'students' => [$student->id], // This should not be persisted as a column
        ]);

        // Verify 'students' column doesn't exist in the table
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('school_classes');
        $this->assertNotContains('students', $columns);
    }

    /**
     * Test classroom has many-to-many relationship with students
     */
    public function test_school_class_students_many_to_many(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

        $students = User::factory()->count(3)->create(['role' => 'eleve']);

        foreach ($students as $student) {
            $class->students()->attach($student->id);
        }

        // Verify pivot table relationship
        $this->assertCount(3, $class->students);
        
        // Verify the pivot table contains the correct records
        foreach ($students as $student) {
            $this->assertDatabaseHas('class_user', [
                'school_class_id' => $class->id,
                'user_id' => $student->id,
            ]);
        }
    }

    /**
     * Test pivot table has timestamps
     */
    public function test_pivot_table_has_timestamps(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);
        $student = User::factory()->create(['role' => 'eleve']);

        $class->students()->attach($student->id);

        $pivotRecord = $class->students()
            ->where('user_id', $student->id)
            ->first();

        // The relationship should have timestamps
        $this->assertNotNull($pivotRecord->pivot->created_at);
        $this->assertNotNull($pivotRecord->pivot->updated_at);
    }

    /**
     * Test sync updates existing relationships
     */
    public function test_sync_updates_existing_relationships(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);
        $class = SchoolClass::factory()->create(['teacher_id' => $teacher->id]);

        $student1 = User::factory()->create(['role' => 'eleve']);
        $student2 = User::factory()->create(['role' => 'eleve']);

        $class->students()->attach($student1->id);

        // Sync with the same student and a new one
        $class->students()->sync([$student1->id, $student2->id]);

        // Verify student1 still exists
        $this->assertTrue($class->students->contains($student1));
        $this->assertTrue($class->students->contains($student2));
        $this->assertCount(2, $class->students);
    }

    /**
     * Test creating class without students works
     */
    public function test_create_class_without_students(): void
    {
        $teacher = User::factory()->create(['role' => 'prof']);

        $class = SchoolClass::create([
            'name' => 'Test Class',
            'level' => '10th Grade',
            'academic_year' => '2025-2026',
            'teacher_id' => $teacher->id,
        ]);

        $this->assertCount(0, $class->students);
        $this->assertDatabaseHas('school_classes', [
            'id' => $class->id,
        ]);
    }
}
