<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'content',
        'image',
    ];


    /*
    |--------------------------------------------------------------------------
    | POST OWNER
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /*
    |--------------------------------------------------------------------------
    | LIKES
    |--------------------------------------------------------------------------
    */

    public function likes()
    {
        return $this->hasMany(
            Like::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | COMMENTS
    |--------------------------------------------------------------------------
    */

    public function comments()
    {
        return $this->hasMany(
            Comment::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | USERS WHO SAVED THIS POST
    |--------------------------------------------------------------------------
    */

    public function savedByUsers()
    {
        return $this->belongsToMany(
            User::class,
            'saved_posts',
            'post_id',
            'user_id'
        )->withTimestamps();
    }

    public function postImages()
{
    return $this->hasMany(
        PostImage::class
    );
}

    /*
|--------------------------------------------------------------------------
| POST IMAGES
|--------------------------------------------------------------------------
*/

public function images()
{
    return $this
        ->hasMany(PostImage::class)
        ->orderBy('position');
}
}