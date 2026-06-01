<?php

namespace App\Policies;

use App\Models\FileMetadata;
use App\Models\User;

class FilePolicy
{
    /**
     * Determine whether the user can view any file.
     */
    public function viewAny(?User $user): bool
    {
        // Admin and teachers can list files
        if ($user?->isAdmin() || $user?->isTeacher()) {
            return true;
        }

        // Students can view (but policies will filter)
        return $user?->isStudent() ?? false;
    }

    /**
     * Determine whether the user can view a specific file.
     */
    public function view(?User $user, FileMetadata $file): bool
    {
        // Admin can always view
        if ($user?->isAdmin()) {
            return true;
        }

        // Uploader can always view their own file
        if ($user && $file->uploaded_by === $user->id) {
            return true;
        }

        // Public files anyone can view
        if ($file->access_level === 'public') {
            return true;
        }

        // Teachers can view class files if the file is for their class
        if ($user?->isTeacher()) {
            if ($file->access_level === 'class' && $file->accessible_to_class_id) {
                return $file->accessibleToClass->teacher_id === $user->id;
            }
            return false;
        }

        // Students can view class files if they're enrolled
        if ($user?->isStudent()) {
            if ($file->access_level === 'class' && $file->accessible_to_class_id) {
                return $file->accessibleToClass->students()->where('users.id', $user->id)->exists();
            }
            return false;
        }

        return false;
    }

    /**
     * Determine whether the user can create a file.
     */
    public function create(?User $user): bool
    {
        // Admin and teachers can upload
        return $user?->isAdmin() || $user?->isTeacher();
    }

    /**
     * Determine whether the user can update a file.
     */
    public function update(?User $user, FileMetadata $file): bool
    {
        // Admin can always update
        if ($user?->isAdmin()) {
            return true;
        }

        // Uploader can update their own file
        if ($user && $file->uploaded_by === $user->id) {
            return true;
        }

        // Teacher who uploaded it can update
        if ($user?->isTeacher() && $file->uploaded_by === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete a file.
     */
    public function delete(?User $user, FileMetadata $file): bool
    {
        // Admin can always delete
        if ($user?->isAdmin()) {
            return true;
        }

        // Uploader can delete their own file
        if ($user && $file->uploaded_by === $user->id) {
            return true;
        }

        // Teacher who uploaded can delete
        if ($user?->isTeacher() && $file->uploaded_by === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can download a file.
     */
    public function download(?User $user, FileMetadata $file): bool
    {
        // Use view permission as basis for download
        return $this->view($user, $file);
    }
}
