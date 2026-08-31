<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNABLE ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'email',
        'password',
        'last_seen_at',
    ];
    


    /*
    |--------------------------------------------------------------------------
    | HIDDEN ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    protected $hidden = [
        'password',
        'remember_token',
    ];


    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTE CASTS
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',

            'last_seen_at' => 'datetime',

            'password' => 'hashed',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | USER PROFILE
    |--------------------------------------------------------------------------
    */

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }


    /*
    |--------------------------------------------------------------------------
    | USER POSTS
    |--------------------------------------------------------------------------
    */

    public function posts()
    {
        return $this->hasMany(Post::class);
    }


    /*
    |--------------------------------------------------------------------------
    | USER COMMENTS
    |--------------------------------------------------------------------------
    */

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /*
|--------------------------------------------------------------------------
| STORIES
|--------------------------------------------------------------------------
*/

public function stories()
{
    return $this->hasMany(
        Story::class
    );
}


    /*
    |--------------------------------------------------------------------------
    | USER LIKES
    |--------------------------------------------------------------------------
    */

    public function likes()
    {
        return $this->hasMany(Like::class);
    }


    /*
    |--------------------------------------------------------------------------
    | SAVED POSTS
    |--------------------------------------------------------------------------
    */

    public function savedPosts()
    {
        return $this->belongsToMany(
            Post::class,
            'saved_posts',
            'user_id',
            'post_id'
        )->withTimestamps();
    }


    /*
    |--------------------------------------------------------------------------
    | USERS WHO FOLLOW ME
    |--------------------------------------------------------------------------
    */

    public function followers()
    {
        return $this->belongsToMany(
            User::class,
            'follows',
            'following_id',
            'follower_id'
        )->withTimestamps();
    }


    /*
    |--------------------------------------------------------------------------
    | USERS I FOLLOW
    |--------------------------------------------------------------------------
    */

    public function following()
    {
        return $this->belongsToMany(
            User::class,
            'follows',
            'follower_id',
            'following_id'
        )->withTimestamps();
    }


    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS
    |--------------------------------------------------------------------------
    */

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }


    /*
    |--------------------------------------------------------------------------
    | SENT MESSAGES
    |--------------------------------------------------------------------------
    */

    public function sentMessages()
    {
        return $this->hasMany(
            Message::class,
            'sender_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RECEIVED MESSAGES
    |--------------------------------------------------------------------------
    */

    public function receivedMessages()
    {
        return $this->hasMany(
            Message::class,
            'receiver_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ONLINE STATUS
    |--------------------------------------------------------------------------
    */

    public function isOnline()
    {
        if (!$this->last_seen_at) {
            return false;
        }

        return $this->last_seen_at
            ->greaterThan(
                now()->subMinutes(2)
            );
    }
}