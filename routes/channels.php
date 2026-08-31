<?php

use Illuminate\Support\Facades\Broadcast;


/*
|--------------------------------------------------------------------------
| NOTIFICATION CHANNEL
|--------------------------------------------------------------------------
*/

Broadcast::channel(

    'notifications.{userId}',

    function (
        $user,
        $userId
    ) {

        return
            (int) $user->id ===
            (int) $userId;

    }

);


/*
|--------------------------------------------------------------------------
| CHAT CHANNEL
|--------------------------------------------------------------------------
*/

Broadcast::channel(

    'chat.{userId}',

    function (
        $user,
        $userId
    ) {

        return
            (int) $user->id ===
            (int) $userId;

    }

);