<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\LikerResource;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Services\LikeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{
    protected $likes;

    public function __construct(LikeService $likes)
    {
        $this->likes = $likes;
    }
    public function reactToPost($postId, Request $request)
    {
        $post = Post::findOrFail($postId);
        $user = Auth::user();

        // Check if user already liked this post
        $existingLike = Like::where('user_id', $user->getKey())
            ->where('user_type', get_class($user))
            ->where('likeable_id', $post->id)
            ->where('likeable_type', Post::class)
            ->first();

        if ($existingLike) {
            // Remove existing like
            $existingLike->delete();
        return $this->successResponse([], __('Post unliked successfully'), 200);
        } else {
            // Create new like
            $like = $this->likes->reactToPost($user, $post, 1);
            return $this->successResponse([], __('Post liked successfully'), 200);
        }
    }

    public function reactToComment($commentId, Request $request)
    {
        $comment = Comment::findOrFail($commentId);
        $user = Auth::user();

        // Check if user already liked this comment
        $existingLike = Like::where('user_id', $user->getKey())
            ->where('user_type', get_class($user))
            ->where('likeable_id', $comment->id)
            ->where('likeable_type', Comment::class)
            ->first();

        if ($existingLike) {
            // Remove existing like
            $existingLike->delete();
            return response()->json(['value' => 0, 'action' => 'unliked']);
        } else {
            // Create new like
            $like = $this->likes->reactToComment($user, $comment, 1);
            return response()->json(['value' => $like->value, 'action' => 'liked']);
        }
    }

    public function likersOfPost( Request $request,$postId)
    {
        $post = Post::findOrFail($postId);

        $per_page = $request->query('per_page', 20);
        try{
            $likers = $this->likes->likersOfPost($post, $per_page);
            if($likers->isEmpty()){
            return $this->paginatedResourceResponse($likers, LikerResource::class, __('Likers fetched successfully'));
            }
            return $this->paginatedResourceResponse($likers, LikerResource::class, __('Likers fetched successfully'));
        }catch(\Exception $e){
            Log::debug($e->getMessage());
           return $this->errorResponse(__('Failed to fetch likers'), 500);
        }
    }
}


