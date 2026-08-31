<?php

namespace App\Events;

use App\Models\User;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class UserStoppedTyping implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;


    public User $user;

    public int $receiverId;


    public function __construct(
        User $user,
        int $receiverId
    ) {

        $this->user =
            $user;

        $this->receiverId =
            $receiverId;

    }


    /*
    |--------------------------------------------------------------------------
    | CHANNEL
    |--------------------------------------------------------------------------
    */

    public function broadcastOn(): array
    {

        return [

            new PrivateChannel(

                'chat.' .
                $this->receiverId

            ),

        ];

    }


    /*
    |--------------------------------------------------------------------------
    | EVENT NAME
    |--------------------------------------------------------------------------
    */

    public function broadcastAs(): string
    {

        return 'user.stopped.typing';

    }


    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    public function broadcastWith(): array
    {

        return [

            'user' => [

                'id' =>
                    $this->user->id,

                'name' =>
                    $this->user->name,

            ],

        ];

    }
}