<?php

namespace App\Http\Controllers;

use App\Models\FileMetadata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    /**
     * Download a file.
     */
    public function download(FileMetadata $file): StreamedResponse|BinaryFileResponse
    {
        // Check authorization
        Gate::authorize('download', $file);

        // Record download
        $file->recordDownload();

        // Stream the file download
        return Storage::disk('local')->download($file->file_path, $file->original_name);
    }

    /**
     * View file content (for PDFs, images, etc).
     */
    public function view(FileMetadata $file): StreamedResponse|BinaryFileResponse
    {
        // Check authorization
        Gate::authorize('view', $file);

        // Return file for inline viewing (not download)
        return Storage::disk('local')->response($file->file_path);
    }

    /**
     * Handle file upload via API.
     * This would typically be called by API Platform POST handler.
     */
    public function store(Request $request): FileMetadata
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,mp4,jpg,png,jpeg,doc,docx|max:102400', // 100MB
            'category' => 'sometimes|string|in:document,pdf,video,image',
            'access_level' => 'sometimes|string|in:private,class,public',
            'accessible_to_class_id' => 'nullable|integer|exists:school_classes,id',
            'description' => 'nullable|string|max:500',
        ]);

        // Check authorization
        Gate::authorize('create', FileMetadata::class);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();
        $fileName = md5($originalName . time()) . '.' . $file->getClientOriginalExtension();

        // Store file
        $path = $file->storeAs('documents/' . date('Y/m/d'), $fileName, 'local');

        // Create metadata record
        return FileMetadata::create([
            'original_name' => $originalName,
            'file_name' => $fileName,
            'file_path' => $path,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'uploaded_by' => auth()->id(),
            'category' => $request->input('category', 'document'),
            'access_level' => $request->input('access_level', 'private'),
            'accessible_to_class_id' => $request->input('accessible_to_class_id'),
            'description' => $request->input('description'),
        ]);
    }
}
