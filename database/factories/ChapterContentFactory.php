<?php

namespace Database\Factories;

use App\Models\Chapter;
use App\Models\ChapterContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChapterContent>
 */
class ChapterContentFactory extends Factory
{
    protected $model = ChapterContent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $contentType = $this->faker->randomElement(['text', 'video', 'quiz']);

        return [
            'chapter_id' => Chapter::factory()->create()->id,
            'title' => $this->faker->sentence(3),
            'content' => $this->faker->paragraph(),
            'content_type' => $contentType,
            'video_url' => $contentType === 'video' ? 'https://example.com/' . $this->faker->slug() . '.mp4' : null,
            'duration_seconds' => $contentType === 'video' ? $this->faker->numberBetween(600, 7200) : null,
            'position' => $this->faker->numberBetween(1, 10),
        ];
    }
}
