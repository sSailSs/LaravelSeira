<?php

namespace Database\Seeders;

use App\Models\ChapterContent;
use App\Models\User;
use App\Models\UserContentProgress;
use Illuminate\Database\Seeder;

class SchoolProgressSeeder extends Seeder
{
    /**
     * Seed demo watch progress for students on video contents.
     */
    public function run(): void
    {
        $students = User::query()->where('role', 'eleve')->orderBy('id')->take(12)->get();
        $videoContents = ChapterContent::query()->where('content_type', 'video')->orderBy('id')->take(12)->get();

        if ($students->isEmpty() || $videoContents->isEmpty()) {
            return;
        }

        foreach ($students as $studentIndex => $student) {
            foreach ($videoContents->slice(0, 4) as $contentIndex => $videoContent) {
                $duration = $videoContent->duration_seconds ?? 0;
                $baseProgress = min($duration, (($studentIndex + 1) * 90) + ($contentIndex * 120));

                UserContentProgress::query()->updateOrCreate(
                    [
                        'user_id' => $student->id,
                        'chapter_content_id' => $videoContent->id,
                    ],
                    [
                        'progress_seconds' => $baseProgress,
                        'is_completed' => $duration > 0 && $baseProgress >= $duration,
                        'last_watched_at' => now()->subDays(($studentIndex + $contentIndex) % 5),
                    ]
                );
            }
        }
    }
}