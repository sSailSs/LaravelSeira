<?php

namespace Database\Factories;

use App\Models\FileMetadata;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FileMetadata>
 */
class FileMetadataFactory extends Factory
{
    protected $model = FileMetadata::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileName = fake()->word() . '.pdf';
        $fileSize = fake()->numberBetween(1000000, 50000000); // 1MB to 50MB

        return [
            'original_name' => $fileName,
            'file_name' => md5($fileName . time()) . '.pdf',
            'file_path' => 'documents/' . date('Y/m/d') . '/' . md5($fileName . time()) . '.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => $fileSize,
            'uploaded_by' => User::factory(),
            'category' => 'pdf',
            'access_level' => fake()->randomElement(['private', 'class', 'public']),
            'description' => fake()->sentence(10),
            'download_count' => fake()->numberBetween(0, 100),
            'last_downloaded_at' => fake()->dateTimeBetween('-30 days'),
        ];
    }

    /**
     * State for public files.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'access_level' => 'public',
            'accessible_to_class_id' => null,
        ]);
    }

    /**
     * State for class-specific files.
     */
    public function forClass(SchoolClass $class): static
    {
        return $this->state(fn (array $attributes) => [
            'access_level' => 'class',
            'accessible_to_class_id' => $class->id,
        ]);
    }

    /**
     * State for private files.
     */
    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'access_level' => 'private',
            'accessible_to_class_id' => null,
        ]);
    }
}
