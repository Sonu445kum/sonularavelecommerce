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
        Schema::table('reviews', function (Blueprint $table) {
            // Add new column for video_path if not exists
            if (!Schema::hasColumn('reviews', 'video_path')) {
                $table->string('video_path')->nullable()->after('images');
            }

            // Ensure rating column type is tinyint unsigned
            $table->unsignedTinyInteger('rating')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('video_path');
        });
    }
};
