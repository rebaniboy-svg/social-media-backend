<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class SavePostController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SAVE / UNSAVE POST
    |--------------------------------------------------------------------------
    */

    public function toggle(
        Request $request,
        Post $post
    ) {

        $user = $request->user();


        $alreadySaved = $user
            ->savedPosts()
            ->where(
                'post_id',
                $post->id
            )
            ->exists();


        /*
        |--------------------------------------------------------------------------
        | UNSAVE
        |--------------------------------------------------------------------------
        */

        if ($alreadySaved) {

            $user
                ->savedPosts()
                ->detach(
                    $post->id
                );


            return response()->json([

                'message' =>
                    'Post removed from saved posts',

                'saved' =>
                    false,

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | SAVE
        |--------------------------------------------------------------------------
        */

        $user
            ->savedPosts()
            ->attach(
                $post->id
            );


        return response()->json([

            'message' =>
                'Post saved successfully',

            'saved' =>
                true,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GET SAVED POSTS
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ) {

        $user = $request->user();


        $posts = $user
            ->savedPosts()
            ->with([
                'user.profile',
            ])
            ->withCount([
                'likes',
                'comments',
            ])
            ->latest()
            ->get();


        $posts->each(function ($post) use ($user) {

            $post->liked_by_user = $post
                ->likes()
                ->where(
                    'user_id',
                    $user->id
                )
                ->exists();


            $post->saved_by_user = true;

        });


        return response()->json([

            'posts' =>
                $posts,

        ]);
    }
}