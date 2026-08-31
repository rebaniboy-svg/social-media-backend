<?php

namespace App\Events;

use App\Models\Message;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;


    public Message $message;


    public function __construct(
        Message $message
    ) {

        $this->message = $message;

    }


    /*
    |--------------------------------------------------------------------------
    | BROADCAST CHANNEL
    |--------------------------------------------------------------------------
    */

    public function broadcastOn(): array
    {

        return [

            new PrivateChannel(

                'chat.' .
                $this->message->receiver_id

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

        return 'message.sent';

    }


    /*
    |--------------------------------------------------------------------------
    | EVENT DATA
    |--------------------------------------------------------------------------
    */

    public function broadcastWith(): array
    {

        $this->message->load(
            'sender.profile'
        );


        return [

            'message' => [

                'id' =>
                    $this->message->id,

                'sender_id' =>
                    $this->message->sender_id,

                'receiver_id' =>
                    $this->message->receiver_id,

                'message' =>
                    $this->message->message,

                'read_at' =>
                    $this->message->read_at,

                'created_at' =>
                    $this->message
                        ->created_at
                        ?->toISOString(),

                'updated_at' =>
                    $this->message
                        ->updated_at
                        ?->toISOString(),

                'sender' =>
                    $this->message->sender,

            ],

        ];

    }
}