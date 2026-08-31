<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Events\UserStoppedTyping;
use App\Events\UserTyping;

use App\Http\Controllers\Controller;

use App\Models\Message;
use App\Models\User;

use Illuminate\Http\Request;


class MessageController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GET CONVERSATION USERS
    |--------------------------------------------------------------------------
    */

    public function users(
        Request $request
    ) {

        $currentUser =
            $request->user();


        /*
        |--------------------------------------------------------------------------
        | GET USERS EXCEPT CURRENT USER
        |--------------------------------------------------------------------------
        */

        $users = User::where(

            'id',

            '!=',

            $currentUser->id

        )
        ->with(

            'profile'

        )
        ->get();


        /*
        |--------------------------------------------------------------------------
        | ADD MESSAGE INFORMATION
        |--------------------------------------------------------------------------
        */

        $users->each(
            function ($user)
            use ($currentUser) {

                /*
                |--------------------------------------------------------------------------
                | LAST MESSAGE
                |--------------------------------------------------------------------------
                */

                $lastMessage =
                    Message::where(
                        function ($query)
                        use (
                            $currentUser,
                            $user
                        ) {

                            $query->where(
                                'sender_id',
                                $currentUser->id
                            )
                            ->where(
                                'receiver_id',
                                $user->id
                            );

                        }
                    )
                    ->orWhere(
                        function ($query)
                        use (
                            $currentUser,
                            $user
                        ) {

                            $query->where(
                                'sender_id',
                                $user->id
                            )
                            ->where(
                                'receiver_id',
                                $currentUser->id
                            );

                        }
                    )
                    ->latest()
                    ->first();


                /*
                |--------------------------------------------------------------------------
                | UNREAD COUNT
                |--------------------------------------------------------------------------
                */

                $unreadCount =
                    Message::where(

                        'sender_id',

                        $user->id

                    )
                    ->where(

                        'receiver_id',

                        $currentUser->id

                    )
                    ->whereNull(

                        'read_at'

                    )
                    ->count();


                /*
                |--------------------------------------------------------------------------
                | ONLINE STATUS
                |--------------------------------------------------------------------------
                */

                $user->is_online =

                    $user->last_seen_at &&

                    $user
                        ->last_seen_at
                        ->greaterThan(

                            now()->subSeconds(60)

                        );


                $user->last_message =
                    $lastMessage;


                $user->unread_count =
                    $unreadCount;

            }
        );


        /*
        |--------------------------------------------------------------------------
        | SORT BY LATEST MESSAGE
        |--------------------------------------------------------------------------
        */

        $users = $users
            ->sortByDesc(
                function ($user) {

                    return
                        $user
                            ->last_message
                            ?->created_at;

                }
            )
            ->values();


        return response()->json([

            'users' =>
                $users,

        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | GET CONVERSATION
    |--------------------------------------------------------------------------
    */

    public function conversation(

        Request $request,

        User $user

    ) {

        $currentUser =
            $request->user();


        /*
        |--------------------------------------------------------------------------
        | GET MESSAGES
        |--------------------------------------------------------------------------
        */

        $messages =
            Message::where(
                function ($query)
                use (
                    $currentUser,
                    $user
                ) {

                    $query->where(

                        'sender_id',

                        $currentUser->id

                    )
                    ->where(

                        'receiver_id',

                        $user->id

                    );

                }
            )
            ->orWhere(
                function ($query)
                use (
                    $currentUser,
                    $user
                ) {

                    $query->where(

                        'sender_id',

                        $user->id

                    )
                    ->where(

                        'receiver_id',

                        $currentUser->id

                    );

                }
            )
            ->oldest()
            ->get();


        /*
        |--------------------------------------------------------------------------
        | MARK RECEIVED MESSAGES AS READ
        |--------------------------------------------------------------------------
        */

        Message::where(

            'sender_id',

            $user->id

        )
        ->where(

            'receiver_id',

            $currentUser->id

        )
        ->whereNull(

            'read_at'

        )
        ->update([

            'read_at' =>
                now(),

        ]);


        return response()->json([

            'messages' =>
                $messages,

        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | SEND MESSAGE
    |--------------------------------------------------------------------------
    */

    public function store(

        Request $request,

        User $user

    ) {

        $validated =
            $request->validate([

                'message' =>
                    'required|string|max:5000',

            ]);


        $currentUser =
            $request->user();


        /*
        |--------------------------------------------------------------------------
        | PREVENT MESSAGE TO SELF
        |--------------------------------------------------------------------------
        */

        if (

            $currentUser->id ===
            $user->id

        ) {

            return response()->json([

                'message' =>
                    'You cannot message yourself.',

            ], 422);

        }


        /*
        |--------------------------------------------------------------------------
        | CREATE MESSAGE
        |--------------------------------------------------------------------------
        */

        $message =
            Message::create([

                'sender_id' =>
                    $currentUser->id,

                'receiver_id' =>
                    $user->id,

                'message' =>
                    $validated['message'],

            ]);


        /*
        |--------------------------------------------------------------------------
        | LOAD SENDER
        |--------------------------------------------------------------------------
        */

        $message->load(

            'sender.profile'

        );


        /*
        |--------------------------------------------------------------------------
        | BROADCAST MESSAGE
        |--------------------------------------------------------------------------
        */

        broadcast(

            new MessageSent(
                $message
            )

        );


        return response()->json([

            'message' =>
                'Message sent successfully.',

            'data' =>
                $message,

        ], 201);

    }


    /*
    |--------------------------------------------------------------------------
    | USER STARTED TYPING
    |--------------------------------------------------------------------------
    */

    public function typing(

        Request $request,

        User $user

    ) {

        $currentUser =
            $request->user();


        /*
        |--------------------------------------------------------------------------
        | BROADCAST TYPING
        |--------------------------------------------------------------------------
        */

        broadcast(

            new UserTyping(

                $currentUser,

                $user->id

            )

        );


        return response()->json([

            'success' =>
                true,

        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | USER STOPPED TYPING
    |--------------------------------------------------------------------------
    */

    public function stoppedTyping(

        Request $request,

        User $user

    ) {

        $currentUser =
            $request->user();


        /*
        |--------------------------------------------------------------------------
        | BROADCAST STOP TYPING
        |--------------------------------------------------------------------------
        */

        broadcast(

            new UserStoppedTyping(

                $currentUser,

                $user->id

            )

        );


        return response()->json([

            'success' =>
                true,

        ]);

    }
}