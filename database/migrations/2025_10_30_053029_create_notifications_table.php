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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index(); // user who receives the notification
            $table->string('type')->nullable(); // e.g. OrderPlaced, PaymentSuccess
            $table->string('title')->nullable(); // short title for admin dashboard display
            $table->text('message')->nullable(); // main message body
            $table->json('data')->nullable(); // extra data like order_id, amount
            $table->boolean('is_read')->default(false); // whether notification was read
            $table->timestamp('read_at')->nullable(); // when it was read
            $table->timestamps();

            // Foreign key relation
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
