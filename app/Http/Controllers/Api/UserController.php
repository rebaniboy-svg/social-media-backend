<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SEARCH USERS
    |--------------------------------------------------------------------------
    */

    public function search(
        Request $request
    ) {
        $validated = $request->validate([
            'query' => 'nullable|string|max:255',
        ]);


        $query =
            $validated['query'] ?? '';


        $users = User::with(
            'profile'
        )
            ->where(
                'id',
                '!=',
                $request->user()->id
            )
            ->when(
                $query,
                function (
                    $builder
                ) use ($query) {

                    $builder
                        ->where(
                            function (
                                $search
                            ) use ($query) {

                                $search
                                    ->where(
                                        'name',
                                        'ILIKE',
                                        '%' .
                                        $query .
                                        '%'
                                    )
                                    ->orWhere(
                                        'email',
                                        'ILIKE',
                                        '%' .
                                        $query .
                                        '%'
                                    );

                            }
                        );

                }
            )
            ->latest()
            ->get();


        $users->each(
            function (
                $user
            ) {

                $user->is_online =
                    $user->isOnline();

            }
        );


        return response()->json([
            'users' => $users,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | HEARTBEAT
    |--------------------------------------------------------------------------
    */

    public function heartbeat(
        Request $request
    ) {
        $user =
            $request->user();


        $user->update([
            'last_seen_at' => now(),
        ]);


        return response()->json([
            'message' =>
                'User activity updated.',

            'last_seen_at' =>
                $user->last_seen_at,

            'is_online' =>
                true,
        ]);
    }
}