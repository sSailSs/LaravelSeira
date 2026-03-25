<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\ChapterContent;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Seeder;

class SchoolStructureSeeder extends Seeder
{
    /**
     * Seed classes, enrollments, courses, chapters and chapter contents.
     */
    public function run(): void
    {
        $teachers = User::query()->where('role', 'prof')->orderBy('id')->get();
        $students = User::query()->where('role', 'eleve')->orderBy('id')->get();

        if ($teachers->isEmpty() || $students->isEmpty()) {
            return;
        }

        $classBlueprints = [
            ['name' => '6A', 'level' => '6eme', 'academic_year' => '2025-2026'],
            ['name' => '6B', 'level' => '6eme', 'academic_year' => '2025-2026'],
            ['name' => '5A', 'level' => '5eme', 'academic_year' => '2025-2026'],
            ['name' => '5B', 'level' => '5eme', 'academic_year' => '2025-2026'],
        ];

        $subjects = ['Mathematiques', 'Francais', 'Histoire', 'SVT'];

        foreach ($classBlueprints as $classIndex => $classBlueprint) {
            $teacher = $teachers[$classIndex % $teachers->count()];

            $class = SchoolClass::query()->create([
                'name' => $classBlueprint['name'],
                'level' => $classBlueprint['level'],
                'academic_year' => $classBlueprint['academic_year'],
                'teacher_id' => $teacher->id,
            ]);

            $studentIds = $students->slice($classIndex * 6, 6)->pluck('id')->all();
            $class->students()->syncWithoutDetaching($studentIds);

            foreach ($subjects as $subject) {
                $course = Course::query()->create([
                    'title' => $subject.' - '.$class->name,
                    'description' => 'Programme annuel de '.$subject.' pour la classe '.$class->name.'.',
                    'school_class_id' => $class->id,
                    'teacher_id' => $teacher->id,
                ]);

                $chapterTitles = [
                    'Introduction',
                    'Approfondissement',
                    'Exercices',
                ];

                foreach ($chapterTitles as $chapterPosition => $chapterTitle) {
                    $chapter = Chapter::query()->create([
                        'course_id' => $course->id,
                        'title' => $chapterTitle,
                        'position' => $chapterPosition + 1,
                    ]);

                    ChapterContent::query()->create([
                        'chapter_id' => $chapter->id,
                        'title' => 'Video de cours',
                        'content' => 'Contenu principal du chapitre '.$chapter->title.'.',
                        'content_type' => 'video',
                        'video_url' => 'https://videos.school.test/'.strtolower(str_replace(' ', '-', $subject)).'/'.$class->name.'/chapitre-'.($chapterPosition + 1).'.mp4',
                        'duration_seconds' => 600 + ($chapterPosition * 180),
                        'position' => 1,
                    ]);

                    ChapterContent::query()->create([
                        'chapter_id' => $chapter->id,
                        'title' => 'Evaluation rapide',
                        'content' => 'Questions de verification pour le chapitre '.$chapter->title.'.',
                        'content_type' => 'text',
                        'position' => 2,
                    ]);
                }
            }
        }
    }
}
