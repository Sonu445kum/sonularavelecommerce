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
        Schema::table('notifications', function (Blueprint $table) {
            // ✅ Rename column from 'read' to 'is_read' only if it exists
            if (Schema::hasColumn('notifications', 'read')) {
                $table->renameColumn('read', 'is_read');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // ✅ Revert back if migration is rolled back, only if 'is_read' exists
            if (Schema::hasColumn('notifications', 'is_read')) {
                $table->renameColumn('is_read', 'read');
            }
        });
    }
};
