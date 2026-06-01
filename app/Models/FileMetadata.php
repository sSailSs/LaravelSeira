<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Database\Factories\FileMetadataFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ApiResource(operations: [
    new GetCollection(),
    new Post(rules: [
        'original_name' => 'sometimes|string|max:255',
        'file' => 'required|file|mimes:pdf,mp4,jpg,png,jpeg,doc,docx|max:102400', // 100MB max
        'file_path' => 'sometimes|string',
        'mime_type' => 'sometimes|string',
        'file_size' => 'sometimes|integer',
        'category' => 'sometimes|string|in:document,pdf,video,image',
        'access_level' => 'sometimes|string|in:private,class,public',
        'accessible_to_class_id' => 'nullable|integer|exists:school_classes,id',
        'description' => 'nullable|string|max:500',
        'fileable_type' => 'nullable|string',
        'fileable_id' => 'nullable|integer',
    ]),
    new Get(),
    new Delete(),
])]
class FileMetadata extends Model
{
    /** @use HasFactory<FileMetadataFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'file_metadata';

    protected $fillable = [
        'original_name',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'uploaded_by',
        'fileable_type',
        'fileable_id',
        'access_level',
        'accessible_to_class_id',
        'description',
        'category',
        'download_count',
        'last_downloaded_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'download_count' => 'integer',
        'last_downloaded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who uploaded the file.
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the class this file is accessible to (if class-specific).
     */
    public function accessibleToClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'accessible_to_class_id');
    }

    /**
     * Get the fileable (polymorphic: Course, Chapter, ChapterContent, etc).
     */
    public function fileable()
    {
        return $this->morphTo();
    }

    /**
     * Format file size for display.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Mark file as downloaded.
     */
    public function recordDownload(): void
    {
        $this->increment('download_count');
        $this->update(['last_downloaded_at' => now()]);
    }

    /**
     * Check if file is PDF.
     */
    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf' || $this->category === 'pdf';
    }

    /**
     * Get download URL path.
     */
    public function getDownloadUrlAttribute(): string
    {
        return route('files.download', $this->id);
    }
}
