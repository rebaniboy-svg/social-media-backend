<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Like / Unlike Post
    |--------------------------------------------------------------------------
    */

    public function toggle(
        Request $request,
        Post $post
    ) {
        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | Check existing like
        |--------------------------------------------------------------------------
        */

        $existingLike = $post
            ->likes()
            ->where(
                'user_id',
                $user->id
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Unlike
        |--------------------------------------------------------------------------
        */

        if ($existingLike) {

            $existingLike->delete();


            return response()->json([
                'message' => 'Post unliked successfully',

                'liked' => false,

                'likes_count' =>
                    $post
                        ->likes()
                        ->count(),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Like
        |--------------------------------------------------------------------------
        */

        $post
            ->likes()
            ->create([
                'user_id' => $user->id,
            ]);


        /*
        |--------------------------------------------------------------------------
        | Create notification
        |--------------------------------------------------------------------------
        */

        NotificationService::create(
            $post->user_id,
            $user->id,
            'like',
            $user->name .
                ' liked your post.',
            $post->id
        );


        /*
        |--------------------------------------------------------------------------
        | Return response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'message' => 'Post liked successfully',

            'liked' => true,

            'likes_count' =>
                $post
                    ->likes()
                    ->count(),
        ]);
    }
}