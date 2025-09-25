<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    protected $posts;

    public function __construct(PostService $posts)
    {
        $this->posts = $posts;
    }
    public function feed(Request $request)
    {
        $user = Auth::user();
        $per_page = $request->query('per_page', 15);
        $posts = $this->posts->listFeed($per_page);
        if($posts->isEmpty()){
            return $this->paginatedResourceResponse($posts, PostResource::class, __('Posts fetched successfully'));
        }

        return $this->paginatedResourceResponse($posts, PostResource::class, __('Posts fetched successfully'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'content' => ['required', 'string'],
            'media' => ['nullable', 'array'],
            'media.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        $user = Auth::user();
        try{
            $post = $this->posts->createForUser($user, $data);
       return  $this->successResponse([], __('Post created successfully, please wait for approval'));
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return $this->handleException($e, __('Failed to create post'));
        }
    }

    public function show($id)
    {
        $post = $this->posts->findWithMeta($id);
        return $this->successResponse(PostResource::make($post->load(['comments.author'])), __('Post fetched successfully'));
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $user = Auth::user();

        // Only post owner can delete
        if ($post->author_id !== $user->getKey() || $post->author_type !== get_class($user)) {
            abort(403, 'You can only delete your own posts');
        }

        $post->delete();
        return response()->json(['message' => 'Post deleted successfully']);
    }


}


