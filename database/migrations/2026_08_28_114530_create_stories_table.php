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
        Schema::create('stories', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | STORY OWNER
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | STORY MEDIA
            |--------------------------------------------------------------------------
            */

            $table->string('media');


            /*
            |--------------------------------------------------------------------------
            | MEDIA TYPE
            |--------------------------------------------------------------------------
            */

            $table->enum(
                'media_type',
                [
                    'image',
                    'video',
                ]
            )->default('image');


            /*
            |--------------------------------------------------------------------------
            | CAPTION
            |--------------------------------------------------------------------------
            */

            $table->text('caption')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | EXPIRATION
            |--------------------------------------------------------------------------
            */

            $table->timestamp(
                'expires_at'
            );


            /*
            |--------------------------------------------------------------------------
            | TIMESTAMPS
            |--------------------------------------------------------------------------
            */

            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index(
                [
                    'user_id',
                    'expires_at',
                ]
            );

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'stories'
        );
    }
};