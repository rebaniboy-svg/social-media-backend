<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Get Post Comments
    |--------------------------------------------------------------------------
    */

    public function index(Post $post)
    {
        $comments = $post
            ->comments()
            ->with(
                'user.profile'
            )
            ->latest()
            ->get();


        return response()->json([
            'comments' => $comments,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Create Comment
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Post $post
    ) {
        $validated = $request->validate([
            'content' =>
                'required|string|max:2000',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Create comment
        |--------------------------------------------------------------------------
        */

        $comment = $post
            ->comments()
            ->create([
                'user_id' =>
                    $request->user()->id,

                'content' =>
                    $validated['content'],
            ]);


        /*
        |--------------------------------------------------------------------------
        | Load user
        |--------------------------------------------------------------------------
        */

        $comment->load(
            'user.profile'
        );


        /*
        |--------------------------------------------------------------------------
        | Create notification
        |--------------------------------------------------------------------------
        */

        $user = $request->user();


        NotificationService::create(
            $post->user_id,
            $user->id,
            'comment',
            $user->name .
                ' commented on your post.',
            $post->id
        );


        /*
        |--------------------------------------------------------------------------
        | Return response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'message' =>
                'Comment added successfully',

            'comment' =>
                $comment,

            'comments_count' =>
                $post
                    ->comments()
                    ->count(),
        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Comment
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        Comment $comment
    ) {
        if (
            $comment->user_id !==
            $request->user()->id
        ) {
            return response()->json([
                'message' =>
                    'You are not authorized to delete this comment.',
            ], 403);
        }


        $postId = $comment->post_id;


        $comment->delete();


        $commentsCount = Comment::where(
            'post_id',
            $postId
        )->count();


        return response()->json([
            'message' =>
                'Comment deleted successfully',

            'comments_count' =>
                $commentsCount,
        ]);
    }
}