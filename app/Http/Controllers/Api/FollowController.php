<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Follow / Unfollow User
    |--------------------------------------------------------------------------
    */

    public function toggle(
        Request $request,
        User $user
    ) {
        $currentUser = $request->user();


        /*
        |--------------------------------------------------------------------------
        | Prevent Following Yourself
        |--------------------------------------------------------------------------
        */

        if (
            $currentUser->id === $user->id
        ) {
            return response()->json([
                'message' =>
                    'You cannot follow yourself.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Check Follow Status
        |--------------------------------------------------------------------------
        */

        $isFollowing = $currentUser
            ->following()
            ->where(
                'users.id',
                $user->id
            )
            ->exists();


        /*
        |--------------------------------------------------------------------------
        | Unfollow
        |--------------------------------------------------------------------------
        */

        if ($isFollowing) {

            $currentUser
                ->following()
                ->detach(
                    $user->id
                );


            /*
            |--------------------------------------------------------------------------
            | Remove Follow Notification
            |--------------------------------------------------------------------------
            */

            Notification::where(
                'user_id',
                $user->id
            )
                ->where(
                    'actor_id',
                    $currentUser->id
                )
                ->where(
                    'type',
                    'follow'
                )
                ->delete();


            return response()->json([
                'message' =>
                    'User unfollowed successfully.',

                'following' =>
                    false,

                'followers_count' =>
                    $user
                        ->followers()
                        ->count(),

                'following_count' =>
                    $currentUser
                        ->following()
                        ->count(),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Follow User
        |--------------------------------------------------------------------------
        */

        $currentUser
            ->following()
            ->attach(
                $user->id
            );


        /*
        |--------------------------------------------------------------------------
        | Create Notification
        |--------------------------------------------------------------------------
        */

        NotificationService::create(
            $user->id,
            $currentUser->id,
            'follow',
            $currentUser->name .
                ' started following you.'
        );


        return response()->json([
            'message' =>
                'User followed successfully.',

            'following' =>
                true,

            'followers_count' =>
                $user
                    ->followers()
                    ->count(),

            'following_count' =>
                $currentUser
                    ->following()
                    ->count(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Followers
    |--------------------------------------------------------------------------
    */

    public function followers(
        User $user
    ) {
        $followers = $user
            ->followers()
            ->with(
                'profile'
            )
            ->latest()
            ->get();


        return response()->json([
            'followers' =>
                $followers,

            'count' =>
                $followers->count(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Get Following
    |--------------------------------------------------------------------------
    */

    public function following(
        User $user
    ) {
        $following = $user
            ->following()
            ->with(
                'profile'
            )
            ->latest()
            ->get();


        return response()->json([
            'following' =>
                $following,

            'count' =>
                $following->count(),
        ]);
    }
}