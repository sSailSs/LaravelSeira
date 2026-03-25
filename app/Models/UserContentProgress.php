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

#[ApiResource(operations: [
    new GetCollection(),
    new Post(rules: [
        'user_id' => 'required_without:user|integer|exists:users,id',
        'user' => ['required_without:user_id', 'regex:/^(\/api\/users\/\d+|\d+)$/'],
        'chapter_content_id' => 'required_without:chapterContent|integer|exists:chapter_contents,id',
        'chapterContent' => ['required_without:chapter_content_id', 'regex:/^(\/api\/chapter_contents\/\d+|\d+)$/'],
        'progress_seconds' => 'sometimes|integer|min:0',
        'is_completed' => 'sometimes|boolean',
        'last_watched_at' => 'sometimes|nullable|date',
    ]),
    new Get(),
    new Patch(rules: [
        'user_id' => 'sometimes|integer|exists:users,id',
        'user' => ['sometimes', 'regex:/^(\/api\/users\/\d+|\d+)$/'],
        'chapter_content_id' => 'sometimes|integer|exists:chapter_contents,id',
        'chapterContent' => ['sometimes', 'regex:/^(\/api\/chapter_contents\/\d+|\d+)$/'],
        'progress_seconds' => 'sometimes|integer|min:0',
        'is_completed' => 'sometimes|boolean',
        'last_watched_at' => 'sometimes|nullable|date',
    ]),
    new Delete(),
])]
class UserContentProgress extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'chapter_content_id',
        'progress_seconds',
        'is_completed',
        'last_watched_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'progress_seconds' => 'integer',
        'is_completed' => 'boolean',
        'last_watched_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chapterContent(): BelongsTo
    {
        return $this->belongsTo(ChapterContent::class);
    }
}