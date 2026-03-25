<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ApiResource(operations: [
    new GetCollection(),
    new Post(rules: [
        'title' => 'required|string|max:255',
        'position' => 'sometimes|integer|min:1',
        'course_id' => 'required_without:course|integer|exists:courses,id',
        'course' => ['required_without:course_id', 'regex:/^(\/api\/courses\/\d+|\d+)$/'],
    ]),
    new Get(),
    new Patch(rules: [
        'title' => 'sometimes|string|max:255',
        'position' => 'sometimes|integer|min:1',
        'course_id' => 'sometimes|integer|exists:courses,id',
        'course' => ['sometimes', 'regex:/^(\/api\/courses\/\d+|\d+)$/'],
    ]),
    new Delete(),
])]
class Chapter extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'position',
        'course_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'position' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(ChapterContent::class);
    }
}
