<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Get Notifications
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ) {
        $user = $request->user();


        $notifications = Notification::where(
            'user_id',
            $user->id
        )
            ->with([
                'actor.profile',
                'post',
            ])
            ->latest()
            ->get();


        $unreadCount = Notification::where(
            'user_id',
            $user->id
        )
            ->where(
                'is_read',
                false
            )
            ->count();


        return response()->json([
            'notifications' =>
                $notifications,

            'unread_count' =>
                $unreadCount,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Mark One Notification As Read
    |--------------------------------------------------------------------------
    */

    public function markAsRead(
        Request $request,
        Notification $notification
    ) {
        if (
            $notification->user_id !==
            $request->user()->id
        ) {
            return response()->json([
                'message' =>
                    'Unauthorized.',
            ], 403);
        }


        $notification->update([
            'is_read' => true,
        ]);


        return response()->json([
            'message' =>
                'Notification marked as read.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Mark All Notifications As Read
    |--------------------------------------------------------------------------
    */

    public function markAllAsRead(
        Request $request
    ) {
        Notification::where(
            'user_id',
            $request->user()->id
        )
            ->where(
                'is_read',
                false
            )
            ->update([
                'is_read' => true,
            ]);


        return response()->json([
            'message' =>
                'All notifications marked as read.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Notification
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        Notification $notification
    ) {
        if (
            $notification->user_id !==
            $request->user()->id
        ) {
            return response()->json([
                'message' =>
                    'Unauthorized.',
            ], 403);
        }


        $notification->delete();


        return response()->json([
            'message' =>
                'Notification deleted successfully.',
        ]);
    }
}