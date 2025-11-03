<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This adds a new nullable JSON column 'featured_images' 
     * to the existing 'products' table to store multiple images.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // ✅ Add a JSON column for storing multiple featured images
            $table->json('featured_images')->nullable()->after('featured_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * This will remove the 'featured_images' column if rolled back.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // ✅ Drop the column if the migration is rolled back
            $table->dropColumn('featured_images');
        });
    }
};
