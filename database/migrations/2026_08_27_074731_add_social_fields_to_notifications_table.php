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

            if (!Schema::hasColumn('notifications', 'actor_id')) {
                $table->foreignId('actor_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('notifications', 'post_id')) {
                $table->foreignId('post_id')
                    ->nullable()
                    ->constrained('posts')
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('notifications', 'comment_id')) {
                $table->foreignId('comment_id')
                    ->nullable()
                    ->constrained('comments')
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('notifications', 'message_id')) {
                $table->foreignId('message_id')
                    ->nullable()
                    ->constrained('messages')
                    ->cascadeOnDelete();
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {

            if (Schema::hasColumn('notifications', 'actor_id')) {
                $table->dropForeign(['actor_id']);
                $table->dropColumn('actor_id');
            }

            if (Schema::hasColumn('notifications', 'post_id')) {
                $table->dropForeign(['post_id']);
                $table->dropColumn('post_id');
            }

            if (Schema::hasColumn('notifications', 'comment_id')) {
                $table->dropForeign(['comment_id']);
                $table->dropColumn('comment_id');
            }

            if (Schema::hasColumn('notifications', 'message_id')) {
                $table->dropForeign(['message_id']);
                $table->dropColumn('message_id');
            }

        });
    }
};