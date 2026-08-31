<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'user_id',
        'post_id',
        'content',
    ];

    /**
     * User who created the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Post being commented on.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}