<?php

namespace Database\Factories;

use App\Models\ChapterContent;
use App\Models\User;
use App\Models\UserContentProgress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserContentProgress>
 */
class UserContentProgressFactory extends Factory
{
    protected $model = UserContentProgress::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(['role' => 'student'])->id,
            'chapter_content_id' => ChapterContent::factory()->create()->id,
            'progress_seconds' => $this->faker->numberBetween(0, 3600),
            'is_completed' => $this->faker->boolean(),
            'last_watched_at' => $this->faker->dateTimeBetween('-30 days'),
        ];
    }
}
