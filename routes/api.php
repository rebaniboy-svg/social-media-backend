<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\SavePostController;
use App\Http\Controllers\Api\StoryController;


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::post(
    '/register',
    [
        AuthController::class,
        'register'
    ]
);

Route::post(
    '/login',
    [
        AuthController::class,
        'login'
    ]
);


/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATED USER
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/user',
        function (Request $request) {

            return response()->json([
                'user' => $request
                    ->user()
                    ->load('profile'),
            ]);

        }
    );


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/logout',
        [
            AuthController::class,
            'logout'
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | STORIES
    |--------------------------------------------------------------------------
    */

    /*
    | GET ALL ACTIVE STORIES
    */

    Route::get(
        '/stories',
        [
            StoryController::class,
            'index'
        ]
    );


    /*
    | CREATE STORY
    */

    Route::post(
        '/stories',
        [
            StoryController::class,
            'store'
        ]
    );


    /*
    | GET STORIES FOR ONE USER
    */

    Route::get(
        '/stories/user/{userId}',
        [
            StoryController::class,
            'userStories'
        ]
    );


    /*
    | DELETE STORY
    */

    Route::delete(
        '/stories/{story}',
        [
            StoryController::class,
            'destroy'
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | STORY VIEWS
    |--------------------------------------------------------------------------
    */

    /*
    | MARK STORY AS VIEWED
    */

    Route::post(
        '/stories/{story}/view',
        [
            StoryController::class,
            'markAsViewed'
        ]
    );


    /*
    | GET STORY VIEWERS
    */

    Route::get(
        '/stories/{story}/viewers',
        [
            StoryController::class,
            'viewers'
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | STORY REACTIONS
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/stories/{story}/reaction',
        [
            StoryController::class,
            'reaction'
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | STORY REPLIES
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/stories/{story}/reply',
        [
            StoryController::class,
            'reply'
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | POSTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/posts',
        [
            PostController::class,
            'index'
        ]
    );


    /*
    | CREATE POST
    */

    Route::post(
        '/posts',
        [
            PostController::class,
            'store'
        ]
    );


    /*
    | EDIT POST
    */

    Route::put(
        '/posts/{post}',
        [
            PostController::class,
            'update'
        ]
    );


    /*
    | DELETE POST
    */

    Route::delete(
        '/posts/{post}',
        [
            PostController::class,
            'destroy'
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | LIKES
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/posts/{post}/like',
        [
            LikeController::class,
            'toggle'
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | COMMENTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/posts/{post}/comments',
        [
            CommentController::class,
            'index'
        ]
    );


    Route::post(
        '/posts/{post}/comments',
        [
            CommentController::class,
            'store'
        ]
    );


    Route::delete(
        '/comments/{comment}',
        [
            CommentController::class,
            'destroy'
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | SAVE POSTS
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/posts/{post}/save',
        [
            SavePostController::class,
            'toggle'
        ]
    );


    Route::get(
        '/saved-posts',
        [
            SavePostController::class,
            'index'
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | USER SEARCH
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/users/search',
        [
            UserController::class,
            'search'
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | MY PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [
            ProfileController::class,
            'myProfile'
        ]
    );


    Route::post(
        '/profile',
        [
            ProfileController::class,
            'update'
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | PUBLIC USER PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/users/{user}',
        [
            ProfileController::class,
            'userProfile'
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | FOLLOW SYSTEM
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/users/{user}/follow',
        [
            FollowController::class,
            'toggle'
        ]
    );


    Route::get(
        '/users/{user}/followers',
        [
            FollowController::class,
            'followers'
        ]
    );


    Route::get(
        '/users/{user}/following',
        [
            FollowController::class,
            'following'
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/notifications',
        [
            NotificationController::class,
            'index'
        ]
    );


    Route::post(
        '/notifications/read-all',
        [
            NotificationController::class,
            'markAllAsRead'
        ]
    );


    Route::post(
        '/notifications/{notification}/read',
        [
            NotificationController::class,
            'markAsRead'
        ]
    );


    Route::delete(
        '/notifications/{notification}',
        [
            NotificationController::class,
            'destroy'
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | MESSAGES
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/messages/users',
        [
            MessageController::class,
            'users'
        ]
    );


    Route::get(
        '/messages/{user}',
        [
            MessageController::class,
            'conversation'
        ]
    );


    Route::post(
        '/messages/{user}',
        [
            MessageController::class,
            'store'
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | MESSAGE TYPING
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/messages/{user}/typing',
        [
            MessageController::class,
            'typing'
        ]
    );


    Route::post(
        '/messages/{user}/stopped-typing',
        [
            MessageController::class,
            'stoppedTyping'
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | ONLINE STATUS
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/user/heartbeat',
        [
            UserController::class,
            'heartbeat'
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | BROADCASTING AUTH
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/broadcasting/auth',
        function (Request $request) {

            return Broadcast::auth(
                $request
            );

        }
    );

});