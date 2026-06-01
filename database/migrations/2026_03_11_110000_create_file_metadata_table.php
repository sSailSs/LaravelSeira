<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('file_metadata', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->string('file_path'); // Stored path on disk
            $table->string('file_name'); // Generated filename
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size'); // bytes
            $table->unsignedBigInteger('uploaded_by')->nullable(); // user_id who uploaded
            $table->foreign('uploaded_by')->references('id')->on('users')->nullOnDelete();
            
            // Polymorphic: can be attached to courses, chapters, etc.
            $table->string('fileable_type')->nullable(); // e.g. Course, Chapter, ChapterContent
            $table->unsignedBigInteger('fileable_id')->nullable();
            
            // Access control
            $table->string('access_level')->default('private'); // private, class, public
            $table->unsignedBigInteger('accessible_to_class_id')->nullable(); // If class-specific
            $table->foreign('accessible_to_class_id')->references('id')->on('school_classes')->nullOnDelete();
            
            $table->string('description')->nullable();
            $table->string('category')->default('document'); // document, pdf, video, image
            
            // Tracking
            $table->integer('download_count')->default(0);
            $table->timestamp('last_downloaded_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('uploaded_by');
            $table->index('category');
            $table->index('access_level');
            $table->index(['fileable_type', 'fileable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_metadata');
    }
};
