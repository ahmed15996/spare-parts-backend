<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;

class CommentService extends BaseService
{
    protected $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
        parent::__construct($comment);
    }

    public function addToPost(Model $user, Post $post, array $data): Comment
    {
        $comment = new Comment();
        $comment->author_id = $user->getKey();
        $comment->author_type = get_class($user);
        $comment->post_id = $post->id;
        $comment->content = $data['content'];
        $comment->save();

        return $comment;
    }
}


