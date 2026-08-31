<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'user_id',
        'post_id',
    ];

    /**
     * User who liked the post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Post that was liked.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}