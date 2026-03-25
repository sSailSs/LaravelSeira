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

#[ApiResource(operations: [
    new GetCollection(),
    new Post(rules: [
        'title' => 'required|string|max:255',
        'author' => 'required|string|max:255',
        'isbn' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'publication_date' => 'nullable|date',
    ]),
    new Get(),
    new Patch(rules: [
        'title' => 'sometimes|string|max:255',
        'author' => 'sometimes|string|max:255',
        'isbn' => 'sometimes|nullable|string|max:255',
        'description' => 'sometimes|nullable|string',
        'publication_date' => 'sometimes|nullable|date',
    ]),
    new Delete(),
])]
class Book extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'isbn',
        'title',
        'description',
        'author',
        'publication_date',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'publication_date' => 'date:Y-m-d',
    ];
}
