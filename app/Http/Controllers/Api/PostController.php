<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GET POSTS
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $posts = Post::with([
            'user.profile',
            'likes',
            'comments',
            'images',
        ])
            ->latest()
            ->get();

        return response()->json([
            'posts' => $posts,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE POST
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'images' => [
                'nullable',
                'array',
                'max:10',
            ],

            'images.*' => [
                'image',
                'mimes:jpg,jpeg,png,gif,webp',
                'max:5120',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | REQUIRE CONTENT OR IMAGE
        |--------------------------------------------------------------------------
        */

        if (
            empty($validated['content']) &&
            !$request->hasFile('images')
        ) {
            return response()->json([
                'message' =>
                    'Please add text or at least one image.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE POST
        |--------------------------------------------------------------------------
        */

        $post = Post::create([
            'user_id' =>
                $request->user()->id,

            'content' =>
                $validated['content'] ?? null,
        ]);


        /*
        |--------------------------------------------------------------------------
        | UPLOAD MULTIPLE IMAGES
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('images')) {

            foreach (
                $request->file('images')
                as $index => $image
            ) {

                if (!$image->isValid()) {
                    continue;
                }


                $imagePath = $image->store(
                    'posts',
                    'public'
                );


                $post->images()->create([

                    'image' =>
                        $imagePath,

                    'position' =>
                        $index,

                ]);

            }

        }


        /*
        |--------------------------------------------------------------------------
        | LOAD RELATIONSHIPS
        |--------------------------------------------------------------------------
        */

        $post->load([
            'user.profile',
            'images',
            'likes',
            'comments',
        ]);


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'message' =>
                'Post created successfully.',

            'post' =>
                $post,
        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE POST
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        Post $post
    ) {

        /*
        |--------------------------------------------------------------------------
        | CHECK OWNERSHIP
        |--------------------------------------------------------------------------
        */

        if (
            $post->user_id !==
            $request->user()->id
        ) {

            return response()->json([
                'message' =>
                    'You are not authorized to delete this post.',
            ], 403);

        }


        /*
        |--------------------------------------------------------------------------
        | DELETE POST IMAGES FROM STORAGE
        |--------------------------------------------------------------------------
        */

        $post->load('images');


        foreach ($post->images as $postImage) {

            if (
                $postImage->image &&
                Storage::disk('public')->exists(
                    $postImage->image
                )
            ) {

                Storage::disk('public')->delete(
                    $postImage->image
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | DELETE POST
        |--------------------------------------------------------------------------
        */

        $post->delete();


        return response()->json([
            'message' =>
                'Post deleted successfully.',
        ]);
    }
}