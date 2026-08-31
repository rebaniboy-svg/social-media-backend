<?php

namespace App\Services;

use App\Events\NotificationCreated;
use App\Models\Notification;

class NotificationService
{
    /*
    |--------------------------------------------------------------------------
    | Create and broadcast notification
    |--------------------------------------------------------------------------
    */

    public static function create(
        $userId,
        $actorId,
        $type,
        $message,
        $postId = null
    ) {
        /*
        |--------------------------------------------------------------------------
        | Never notify yourself
        |--------------------------------------------------------------------------
        */

        if (
            (int) $userId ===
            (int) $actorId
        ) {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Save notification
        |--------------------------------------------------------------------------
        */

        $notification = Notification::create([
            'user_id' =>
                $userId,

            'actor_id' =>
                $actorId,

            'post_id' =>
                $postId,

            'type' =>
                $type,

            'message' =>
                $message,

            'is_read' =>
                false,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Load relationships
        |--------------------------------------------------------------------------
        */

        $notification->load([
            'actor.profile',
            'post',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Broadcast notification
        |--------------------------------------------------------------------------
        */

        broadcast(
            new NotificationCreated(
                $notification
            )
        );


        return $notification;
    }
}