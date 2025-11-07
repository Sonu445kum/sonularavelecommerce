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
            // ⭐ Add a position column for drag & drop ordering
            $table->unsignedInteger('position')->default(0)->after('is_approved')->comment('Position for drag & drop ordering');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // ✅ Drop the position column if rolling back
            $table->dropColumn('position');
        });
    }
};
