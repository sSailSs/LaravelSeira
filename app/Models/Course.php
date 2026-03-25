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
        'description' => 'nullable|string',
        'school_class_id' => 'required_without:schoolClass|integer|exists:school_classes,id',
        'schoolClass' => ['required_without:school_class_id', 'regex:/^(\/api\/school_classes\/\d+|\d+)$/'],
        'teacher_id' => 'nullable|integer|exists:users,id',
        'teacher' => ['sometimes', 'regex:/^(\/api\/users\/\d+|\d+)$/'],
    ]),
    new Get(),
    new Patch(rules: [
        'title' => 'sometimes|string|max:255',
        'description' => 'sometimes|nullable|string',
        'school_class_id' => 'sometimes|integer|exists:school_classes,id',
        'schoolClass' => ['sometimes', 'regex:/^(\/api\/school_classes\/\d+|\d+)$/'],
        'teacher_id' => 'sometimes|nullable|integer|exists:users,id',
        'teacher' => ['sometimes', 'regex:/^(\/api\/users\/\d+|\d+)$/'],
    ]),
    new Delete(),
])]
class Course extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'school_class_id',
        'teacher_id',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }
}
