<?php

namespace App\Models;

use App\State\SchoolClassProcessor;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ApiResource(operations: [
    new GetCollection(),
    new Post(
        processor: SchoolClassProcessor::class,
        rules: [
            'name' => 'required|string|max:255',
            'level' => 'nullable|string|max:255',
            'academic_year' => 'nullable|string|max:255',
            'teacher_id' => 'nullable|integer|exists:users,id',
            'teacher' => ['sometimes', 'regex:/^(\/api\/users\/\d+|\d+)$/'],
            'students' => 'sometimes|nullable|array',
            'students.*' => ['regex:/^(\/api\/users\/\d+|\d+)$/'],
        ]
    ),
    new Get(),
    new Patch(
        processor: SchoolClassProcessor::class,
        rules: [
            'name' => 'sometimes|string|max:255',
            'level' => 'sometimes|nullable|string|max:255',
            'academic_year' => 'sometimes|nullable|string|max:255',
            'teacher_id' => 'sometimes|nullable|integer|exists:users,id',
            'teacher' => ['sometimes', 'regex:/^(\/api\/users\/\d+|\d+)$/'],
            'students' => 'sometimes|nullable|array',
            'students.*' => ['regex:/^(\/api\/users\/\d+|\d+)$/'],
        ]
    ),
    new Delete(),
])]
class SchoolClass extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'level',
        'academic_year',
        'teacher_id',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_user', 'school_class_id', 'user_id')
            ->withTimestamps();
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
