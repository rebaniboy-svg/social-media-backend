<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Get authenticated user's profile.
     */
    public function myProfile(Request $request)
    {
        $user = $request->user()->load('profile');

        return response()->json([
            'user' => $user,
        ]);
    }


    /**
     * Get another user's public profile.
     */
    public function userProfile(User $user)
    {
        $user->load([
            'profile',
            'posts' => function ($query) {
                $query->latest();
            },
        ]);

        return response()->json([
            'user' => $user,
        ]);
    }


    /**
     * Update authenticated user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ],

            'username' => [
                'sometimes',
                'nullable',
                'string',
                'max:50',
                Rule::unique('profiles', 'username')
                    ->ignore(
                        optional($user->profile)->id
                    ),
            ],

            'bio' => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],

            'profile_picture' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'cover_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | UPDATE USER
        |--------------------------------------------------------------------------
        */

        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }

        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }

        $user->save();


        /*
        |--------------------------------------------------------------------------
        | GET OR CREATE PROFILE
        |--------------------------------------------------------------------------
        */

        $profile = $user->profile;

        if (!$profile) {
            $profile = $user->profile()->create([
                'username' => null,
                'bio' => null,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE USERNAME
        |--------------------------------------------------------------------------
        */

        if (array_key_exists('username', $validated)) {
            $profile->username =
                $validated['username'];
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE BIO
        |--------------------------------------------------------------------------
        */

        if (array_key_exists('bio', $validated)) {
            $profile->bio =
                $validated['bio'];
        }


        /*
        |--------------------------------------------------------------------------
        | PROFILE PICTURE
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('profile_picture')) {

            $path = $request
                ->file('profile_picture')
                ->store(
                    'profile_pictures',
                    'public'
                );

            $profile->profile_picture = $path;
        }


        /*
        |--------------------------------------------------------------------------
        | COVER PHOTO
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('cover_photo')) {

            $path = $request
                ->file('cover_photo')
                ->store(
                    'cover_photos',
                    'public'
                );

            $profile->cover_photo = $path;
        }


        $profile->save();


        /*
        |--------------------------------------------------------------------------
        | RETURN UPDATED USER
        |--------------------------------------------------------------------------
        */

        $user->load('profile');


        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }


    /**
     * Change password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => [
                'required',
                'string',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);


        $user = $request->user();


        if (
            !Hash::check(
                $request->current_password,
                $user->password
            )
        ) {
            return response()->json([
                'message' =>
                    'Current password is incorrect.',
            ], 422);
        }


        $user->password =
            Hash::make(
                $request->password
            );

        $user->save();


        return response()->json([
            'message' =>
                'Password changed successfully.',
        ]);
    }
}