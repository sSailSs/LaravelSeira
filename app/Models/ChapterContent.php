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
        'chapter_id' => 'required_without:chapter|integer|exists:chapters,id',
        'chapter' => ['required_without:chapter_id', 'regex:/^(\/api\/chapters\/\d+|\d+)$/'],
        'title' => 'nullable|string|max:255',
        'content' => 'required|string',
        'content_type' => 'sometimes|string|in:text,video',
        'video_url' => 'nullable|required_if:content_type,video|url|max:2048',
        'duration_seconds' => 'nullable|required_if:content_type,video|integer|min:1',
        'position' => 'sometimes|integer|min:1',
    ]),
    new Get(),
    new Patch(rules: [
        'chapter_id' => 'sometimes|integer|exists:chapters,id',
        'chapter' => ['sometimes', 'regex:/^(\/api\/chapters\/\d+|\d+)$/'],
        'title' => 'sometimes|nullable|string|max:255',
        'content' => 'sometimes|string',
        'content_type' => 'sometimes|string|in:text,video',
        'video_url' => 'sometimes|nullable|required_if:content_type,video|url|max:2048',
        'duration_seconds' => 'sometimes|nullable|required_if:content_type,video|integer|min:1',
        'position' => 'sometimes|integer|min:1',
    ]),
    new Delete(),
])]
class ChapterContent extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'chapter_id',
        'title',
        'content',
        'content_type',
        'video_url',
        'duration_seconds',
        'position',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'position' => 'integer',
        'duration_seconds' => 'integer',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function progressRecords(): HasMany
    {
        return $this->hasMany(UserContentProgress::class);
    }
}
