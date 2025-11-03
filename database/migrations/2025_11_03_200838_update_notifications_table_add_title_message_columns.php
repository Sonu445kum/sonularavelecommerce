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
            // ✅ Add new columns
            $table->string('title')->nullable()->after('type');
            $table->text('message')->nullable()->after('title');

            // ✅ Rename 'read' → 'is_read'
            if (Schema::hasColumn('notifications', 'read')) {
                $table->renameColumn('read', 'is_read');
            }

            // ✅ Add 'read_at' column
            $table->timestamp('read_at')->nullable()->after('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // ✅ Drop the newly added columns
            $table->dropColumn(['title', 'message', 'read_at']);

            // ✅ Rename back to original column if exists
            if (Schema::hasColumn('notifications', 'is_read')) {
                $table->renameColumn('is_read', 'read');
            }
        });
    }
};
