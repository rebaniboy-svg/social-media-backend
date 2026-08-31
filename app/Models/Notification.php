<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'actor_id',
        'type',
        'message',
        'post_id',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Notification owner
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /*
    |--------------------------------------------------------------------------
    | User who caused the notification
    |--------------------------------------------------------------------------
    */

    public function actor()
    {
        return $this->belongsTo(
            User::class,
            'actor_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Related post
    |--------------------------------------------------------------------------
    */

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}