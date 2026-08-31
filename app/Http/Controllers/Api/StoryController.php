<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GET ALL ACTIVE STORIES
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $stories = Story::with([
            'user.profile',
        ])
            ->where(
                'expires_at',
                '>',
                now()
            )
            ->latest()
            ->get();

        return response()->json([
            'stories' => $stories,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE STORY
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'media' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm',
                'max:20480',
            ],

            'caption' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | GET MIME TYPE
        |--------------------------------------------------------------------------
        */

        $file =
            $request->file('media');

        $mimeType =
            $file->getMimeType();


        /*
        |--------------------------------------------------------------------------
        | DETERMINE MEDIA TYPE
        |--------------------------------------------------------------------------
        */

        if (
            str_starts_with(
                $mimeType,
                'video/'
            )
        ) {

            $mediaType =
                'video';

        } else {

            $mediaType =
                'image';

        }


        /*
        |--------------------------------------------------------------------------
        | STORE FILE
        |--------------------------------------------------------------------------
        */

        $mediaPath =
            $file->store(
                'stories',
                'public'
            );


        /*
        |--------------------------------------------------------------------------
        | CREATE STORY
        |--------------------------------------------------------------------------
        */

        $story =
            Story::create([
                'user_id' =>
                    $request
                        ->user()
                        ->id,

                'media' =>
                    $mediaPath,

                'media_type' =>
                    $mediaType,

                'caption' =>
                    $validated['caption']
                    ?? null,

                'expires_at' =>
                    now()->addHours(24),
            ]);


        /*
        |--------------------------------------------------------------------------
        | LOAD USER
        |--------------------------------------------------------------------------
        */

        $story->load(
            'user.profile'
        );


        return response()->json([
            'message' =>
                'Story created successfully.',

            'story' =>
                $story,
        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE STORY
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        Story $story
    ) {

        /*
        |--------------------------------------------------------------------------
        | CHECK OWNER
        |--------------------------------------------------------------------------
        */

        if (
            (int) $story->user_id !==
            (int) $request->user()->id
        ) {

            return response()->json([
                'message' =>
                    'You can only delete your own story.',
            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | DELETE MEDIA FILE
        |--------------------------------------------------------------------------
        */

        if (
            $story->media &&
            Storage::disk('public')->exists(
                $story->media
            )
        ) {

            Storage::disk('public')->delete(
                $story->media
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DELETE STORY
        |--------------------------------------------------------------------------
        */

        $story->delete();


        return response()->json([
            'message' =>
                'Story deleted successfully.',
        ]);
    }
}