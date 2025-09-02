<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class LikeService extends BaseService
{
    protected $like;

    public function __construct(Like $like)
    {
        $this->like = $like;
        parent::__construct($like);
    }

    public function reactToPost(Model $user, Post $post, int $value): Like
    {
        return Like::updateOrCreate([
            'user_id' => $user->getKey(),
            'user_type' => get_class($user),
            'likeable_id' => $post->id,
            'likeable_type' => Post::class,
        ], [
            'value' => $value,
        ]);
    }

    public function reactToComment(Model $user, Comment $comment, int $value): Like
    {
        return Like::updateOrCreate([
            'user_id' => $user->getKey(),
            'user_type' => get_class($user),
            'likeable_id' => $comment->id,
            'likeable_type' => Comment::class,
        ], [
            'value' => $value,
        ]);
    }

    public function likersOfPost(Post $post, int $perPage = 20): LengthAwarePaginator
    {
        return $this->like->newQuery()
            ->where('likeable_id', $post->id)
            ->where('likeable_type', Post::class)
            ->where('value', 1)
            ->with('user') // Load the user relationship
            ->paginate($perPage);
    }
}


