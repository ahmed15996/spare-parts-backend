<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    protected $comments;

    public function __construct(CommentService $comments)
    {
        $this->comments = $comments;
    }
    public function store($postId, Request $request)
    {
        $data = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $post = Post::findOrFail($postId);
        $user = Auth::user();

        // Check if user already commented on this post
        $existingComment = Comment::where('post_id', $postId)
            ->where('author_id', $user->getKey())
            ->where('author_type', get_class($user))
            ->first();

        if ($existingComment) {
            return $this->errorResponse(__('You have already commented on this post'), 422);
        }

        try{

        $comment = $this->comments->addToPost($user, $post, $data);
        return $this->successResponse([], __('Comment added successfully'));
        }catch(\Exception $e){
            return $this->handleException($e, __('Failed to add comment'));
        }
    }

    public function destroy($postId, $commentId)
    {
        $comment = Comment::with('post')->find($commentId);
        if(!$comment){
            return $this->errorResponse(__('Comment not found'), 404);
    }
        $user = Auth::user();

        // Allow deletion if user is post owner OR comment author
        $isPostOwner = ($comment->post->author_id === $user->getKey() && $comment->post->author_type === get_class($user));
        $isCommentAuthor = ($comment->author_id === $user->getKey() && $comment->author_type === get_class($user));

        if (!$isPostOwner && !$isCommentAuthor) {
            return $this->errorResponse(__('You can only delete your own comments or comments on your posts'), 403);
        }

        $comment->delete();
        return $this->successResponse([], __('Comment deleted successfully'));
    }
}


