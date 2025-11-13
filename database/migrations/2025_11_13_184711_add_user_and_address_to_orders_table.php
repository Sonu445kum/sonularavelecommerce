<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'user_id')) {
                $table->foreignId('user_id')
                      ->nullable()
                      ->constrained('users')
                      ->nullOnDelete()
                      ->cascadeOnUpdate();
            }

            if (!Schema::hasColumn('orders', 'address_id')) {
                $table->foreignId('address_id')
                      ->nullable()
                      ->constrained('addresses')
                      ->nullOnDelete()
                      ->cascadeOnUpdate();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('orders', 'address_id')) {
                $table->dropForeign(['address_id']);
                $table->dropColumn('address_id');
            }
        });
    }
};
