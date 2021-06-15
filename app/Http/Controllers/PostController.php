<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response;

use App\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the posts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Post::paginate(5);
    }

     /**
     * Display the post.
     *
     * @param  Post  $post_id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post_id)
    {
        return $post_id;
    }

    /**
     * Store a newly created post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $credentials = $request->only('title', 'content', 'categories');

        $validator = Validator::make($credentials, [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'categories' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid data'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $title = $request->title;
        $content = $request->content;
        $categories = $request->categories;

        Post::create([
            'title' => $title,
            'content' => $content,
            'categories' => $categories,
        ]);

        return response()->json([
            'message' => 'Post created.'
        ], Response::HTTP_CREATED);
    }

    /**
     * Update the post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Post  $post_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post_id)
    {
        $credentials = $request->only('title', 'content', 'categories');

        $validator = Validator::make($credentials, [
            'title' => ['string', 'max:255'],
            'content' => ['string'],
            'categories' => ['string']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid data'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $title = $request->title;
        $content = $request->content;
        $categories = $request->categories;

        if (!$title && !$content && !$categories) {
            return response()->json([
                'message' => 'Http bad request.'
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($title) {
            $post_id->title = $title;
        }
        if ($content) {
            $post_id->content = $content;
        }
        if ($categories) {
            $post_id->categories = $categories;
        }

        $post_id->save();

        return response()->json([
            'message' => 'Post updated.'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the post from storage.
     *
     * @param  Post  $post_id
     * @return \Illuminate\Http\Response
     */
    public function delete(Post $post_id)
    {
        $post_id->delete();

        return response()->json([
            'message' => 'Post removed'
        ], Response::HTTP_OK);
    }
}
