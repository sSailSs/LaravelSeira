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
        Schema::table('chapter_contents', function (Blueprint $table) {
            $table->string('content_type')->default('text')->after('content');
            $table->string('video_url')->nullable()->after('content_type');
            $table->unsignedInteger('duration_seconds')->nullable()->after('video_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chapter_contents', function (Blueprint $table) {
            $table->dropColumn(['content_type', 'video_url', 'duration_seconds']);
        });
    }
};