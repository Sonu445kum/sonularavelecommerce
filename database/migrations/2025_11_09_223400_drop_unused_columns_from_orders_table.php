<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign keys safely
            if (Schema::hasColumn('orders', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('orders', 'address_id')) {
                $table->dropForeign(['address_id']); // drop foreign key first
                $table->dropColumn('address_id');
            }

            // Drop other unused columns
            foreach (['name', 'phone', 'pincode', 'address'] as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add columns back
            if (!Schema::hasColumn('orders', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }

            if (!Schema::hasColumn('orders', 'address_id')) {
                $table->unsignedBigInteger('address_id')->nullable();
                $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
            }

            foreach (['name', 'phone', 'pincode', 'address'] as $col) {
                if (!Schema::hasColumn('orders', $col)) {
                    $table->string($col)->nullable();
                }
            }
        });
    }
};
