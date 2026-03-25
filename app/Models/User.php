<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[ApiResource(operations: [
    new GetCollection(),
    new Post(rules: [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'nullable|string|min:8|max:255',
        'role' => 'sometimes|string|in:admin,prof,eleve,teacher,student',
    ]),
    new Get(),
    new Patch(rules: [
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|max:255|unique:users,email',
        'password' => 'sometimes|nullable|string|min:8|max:255',
        'role' => 'sometimes|string|in:admin,prof,eleve,teacher,student',
    ]),
    new Delete(),
])]
class User extends Authenticatable
{
    public const ROLE_ADMIN = 'admin';

    public const ROLE_TEACHER = 'prof';

    public const ROLE_STUDENT = 'eleve';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function taughtCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function classesAsTeacher(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'teacher_id');
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_user', 'user_id', 'school_class_id')
            ->withTimestamps();
    }

    public function contentProgress(): HasMany
    {
        return $this->hasMany(UserContentProgress::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isTeacher(): bool
    {
        return $this->hasRole(self::ROLE_TEACHER);
    }

    public function isStudent(): bool
    {
        return $this->hasRole(self::ROLE_STUDENT);
    }

    public function hasRole(string $role): bool
    {
        return self::normalizeRole($this->role) === self::normalizeRole($role);
    }

    private static function normalizeRole(?string $role): ?string
    {
        return match ($role) {
            'teacher' => self::ROLE_TEACHER,
            'student' => self::ROLE_STUDENT,
            default => $role,
        };
    }
}
