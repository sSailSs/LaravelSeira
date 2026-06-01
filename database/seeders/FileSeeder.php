<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\FileMetadata;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    /**
     * Seed file metadata for courses and classes.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $teachers = User::where('role', 'prof')->get();
        $classes = SchoolClass::all();
        $courses = Course::all();

        // Create public course materials
        foreach ($courses->take(4) as $course) {
            $teacher = $teachers->random();

            // Course syllabus (public)
            FileMetadata::factory()
                ->public()
                ->create([
                    'original_name' => 'Syllabus_' . $course->title . '.pdf',
                    'file_name' => 'syllabus_' . md5($course->title) . '.pdf',
                    'uploaded_by' => $teacher->id,
                    'description' => 'Course syllabus and requirements for ' . $course->title,
                    'fileable_type' => Course::class,
                    'fileable_id' => $course->id,
                ]);

            // Course materials (class-specific)
            FileMetadata::factory()
                ->forClass($course->schoolClass)
                ->create([
                    'original_name' => 'Materials_' . $course->title . '.pdf',
                    'file_name' => 'materials_' . md5($course->title) . '.pdf',
                    'uploaded_by' => $teacher->id,
                    'description' => 'Study materials for ' . $course->title,
                    'category' => 'document',
                    'fileable_type' => Course::class,
                    'fileable_id' => $course->id,
                ]);

            // Exam preparation guide (private - teacher only)
            FileMetadata::factory()
                ->private()
                ->create([
                    'original_name' => 'ExamGuide_' . $course->title . '.pdf',
                    'file_name' => 'exam_guide_' . md5($course->title) . '.pdf',
                    'uploaded_by' => $teacher->id,
                    'description' => 'Teacher-only exam preparation guidelines',
                    'fileable_type' => Course::class,
                    'fileable_id' => $course->id,
                ]);
        }

        // Create class-level documents
        foreach ($classes as $class) {
            $classTeacher = $class->teacher;

            // Class schedule (class-specific)
            FileMetadata::factory()
                ->forClass($class)
                ->create([
                    'original_name' => 'Schedule_' . $class->name . '.pdf',
                    'file_name' => 'schedule_' . md5($class->name) . '.pdf',
                    'uploaded_by' => $classTeacher->id,
                    'description' => 'Class schedule and timetable for ' . $class->name,
                ]);

            // Class attendance policy (public)
            FileMetadata::factory()
                ->public()
                ->create([
                    'original_name' => 'AttendancePolicy.pdf',
                    'file_name' => 'attendance_policy_' . $class->id . '.pdf',
                    'uploaded_by' => $admin->id,
                    'description' => 'School attendance policy for all classes',
                ]);
        }

        // Create random reference documents
        $subjects = ['Mathématiques', 'Français', 'Histoire', 'SVT'];
        foreach ($subjects as $subject) {
            FileMetadata::factory()
                ->public()
                ->create([
                    'original_name' => $subject . '_Reference.pdf',
                    'file_name' => strtolower(str_replace(' ', '_', $subject)) . '_reference.pdf',
                    'uploaded_by' => $admin->id,
                    'description' => 'Reference materials for ' . $subject,
                    'category' => 'pdf',
                ]);
        }
    }
}
