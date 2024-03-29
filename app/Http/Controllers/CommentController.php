<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Comment;
use App\Like;

class CommentController extends Controller
{
    /**
     * Display the comment.
     *
     * @param  Comment  $comment_id
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment_id)
    {
        return $comment_id;
    }

    /**
     * Display the post likes.
     *
     * @param  Comment  $comment_id
     * @return \Illuminate\Http\Response
     */
    public function likes(Comment $comment_id)
    {
        return $comment_id->likes;
    }

    /**
     * Store a newly created like of comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Comment  $comment_id
     * @return \Illuminate\Http\Response
     */
    public function like(Comment $comment_id, Request $request)
    {
        $query = Like::where('comment_id', '=', $comment_id->id)->where('author', '=', auth()->user()->login)->first();

        if ($query) {
            return response()->json([
                'message' => 'Like already'
            ], Response::HTTP_BAD_REQUEST);
        }

        $credentials = $request->only('type');

        $validator = Validator::make($credentials, [
            'type' => ['required', 'string', 'in:like,dislike']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid data'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $comment_id->likes()->create([
            'author' => auth()->user()->login,
            'type' => $request->type,
        ]);

        return response()->json([
            'message' => 'Like created'
        ], Response::HTTP_CREATED);
    }

    /**
     * Update the comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Comment  $comment_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment_id)
    {
        if (auth()->user()->login != $comment_id->author) {
            return response()->json([
                'message' => 'Comment not avaible'
            ], Response::HTTP_BAD_REQUEST);
        }

        $credentials = $request->only('content');

        $validator = Validator::make($credentials, [
            'content' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid data'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $comment_id->update([
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Comment updated'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the comment from storage.
     *
     * @param  Comment  $comment_id
     * @return \Illuminate\Http\Response
     */
    public function delete(Comment $comment_id)
    {
        if (auth()->user()->role != 'admin' || auth()->user()->login != $comment_id->author) {
            return response()->json([
                'message' => 'Not work'
            ], Response::HTTP_FORBIDDEN);
        }

        $comment_id->likes()->delete();
        $comment_id->delete();

        return response()->json([
            'message' => 'Comment removed'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the likes of post from storage.
     *
     * @param  Post  $comment_id
     * @return \Illuminate\Http\Response
     */
    public function deleteLike(Comment $comment_id)
    {
        $query = Like::where('comment_id', '=', $comment_id->id)->where('author', '=', auth()->user()->login)->first();

        if (!$query) {
            return response()->json([
                'message' => 'Not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $query->delete();

        return response()->json([
            'message' => 'Like removed'
        ], Response::HTTP_OK);
    }
}
