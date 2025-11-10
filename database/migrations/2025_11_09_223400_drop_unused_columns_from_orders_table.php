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
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign key safely
            if (Schema::hasColumn('orders', 'user_id')) {
                $table->dropForeign(['user_id']); // safer than hardcoding name
                $table->dropColumn('user_id');
            }

            // Drop other unused columns safely
            foreach (['name','phone','pincode','address','address_id'] as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add columns back in case of rollback
            if (!Schema::hasColumn('orders', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }

            foreach (['name','phone','pincode','address','address_id'] as $col) {
                if (!Schema::hasColumn('orders', $col)) {
                    $table->string($col)->nullable();
                }
            }
        });
    }
};
