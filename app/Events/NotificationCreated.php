<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable;
    use SerializesModels;

    public $notification;


    public function __construct(
        Notification $notification
    ) {
        $this->notification = $notification;
    }


    /*
    |--------------------------------------------------------------------------
    | Private notification channel
    |--------------------------------------------------------------------------
    */

    public function broadcastOn()
    {
        return new PrivateChannel(
            'user.' .
            $this->notification->user_id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Event name
    |--------------------------------------------------------------------------
    */

    public function broadcastAs()
    {
        return 'notification.created';
    }


    /*
    |--------------------------------------------------------------------------
    | Broadcast data
    |--------------------------------------------------------------------------
    */

    public function broadcastWith()
    {
        $notification = $this->notification
            ->load([
                'actor.profile',
                'post',
            ]);


        return [
            'notification' => [
                'id' =>
                    $notification->id,

                'user_id' =>
                    $notification->user_id,

                'actor_id' =>
                    $notification->actor_id,

                'post_id' =>
                    $notification->post_id,

                'type' =>
                    $notification->type,

                'message' =>
                    $notification->message,

                'is_read' =>
                    $notification->is_read,

                'created_at' =>
                    $notification->created_at,

                'actor' =>
                    $notification->actor,

                'post' =>
                    $notification->post,
            ],
        ];
    }
}