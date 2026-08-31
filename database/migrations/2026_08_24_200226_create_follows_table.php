<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follows', function (Blueprint $table) {
            $table->id();

            // User who follows another user
            $table->foreignId('follower_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // User being followed
            $table->foreignId('following_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            // Prevent following the same user twice
            $table->unique([
                'follower_id',
                'following_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};