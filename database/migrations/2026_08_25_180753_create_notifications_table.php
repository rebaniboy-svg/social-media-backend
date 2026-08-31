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

            // User receiving the notification
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // User who performed the action
            $table->foreignId('actor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Notification type: like, comment, follow
            $table->string('type');

            // Optional related post
            $table->foreignId('post_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            // Notification message
            $table->string('message');

            // Read / unread status
            $table->boolean('is_read')
                ->default(false);

            $table->timestamps();
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