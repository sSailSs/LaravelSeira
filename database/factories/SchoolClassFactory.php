<?php

namespace Database\Factories;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchoolClass>
 */
class SchoolClassFactory extends Factory
{
    protected $model = SchoolClass::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' Class',
            'level' => $this->faker->randomElement(['9th Grade', '10th Grade', '11th Grade', '12th Grade']),
            'academic_year' => '2025-2026',
            'teacher_id' => User::factory()->create(['role' => 'teacher'])->id,
        ];
    }
}
