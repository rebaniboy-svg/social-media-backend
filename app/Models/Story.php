<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Story extends Model
{
    use HasFactory;


    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'user_id',
        'media',
        'media_type',
        'caption',
        'expires_at',
    ];


    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    /*
    |--------------------------------------------------------------------------
    | USER
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VIEWS
    |--------------------------------------------------------------------------
    */

    public function views()
    {
        return $this->hasMany(
            StoryView::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | REACTIONS
    |--------------------------------------------------------------------------
    */

    public function reactions()
    {
        return $this->hasMany(
            StoryReaction::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | REPLIES
    |--------------------------------------------------------------------------
    */

    public function replies()
    {
        return $this->hasMany(
            StoryReply::class
        );
    }
}