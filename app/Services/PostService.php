<?php

namespace App\Services;

use App\Models\Post;
use App\Enums\PostStatus;
use Filament\Notifications\Actions\Action;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PostService extends BaseService
{
    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
        parent::__construct($post);
    }

    public function listMine(Model $user, int $perPage = 15): LengthAwarePaginator
    {
        return $this->post->newQuery()
            ->where('author_id', $user->getKey())
            ->where('author_type', get_class($user))
            ->latest('id')
            ->paginate($perPage);
    }

    public function createForUser(Model $user, array $data): Post
    {
        $post = new Post();
        $post->author_id = $user->getKey();
        $post->author_type = get_class($user);
        $post->content = $data['content'];
        $post->status = PostStatus::Pending->value;
        $post->save();

        $media = $data['media'] ?? null;
        if($media){
            if(is_array($media)){
                foreach($media as $image){
                    $post->addMedia($image)->toMediaCollection('posts');
                }
            } else {
                $post->addMedia($media)->toMediaCollection('posts');
            }
        }
        $this->afterCreate($post);

        return $post;
    }

    public function findWithMeta(int $id): Post
    {
        return $this->post->newQuery()
            ->withCount(['comments', 'likes as likes_count' => function ($q) { $q->where('value', 1); }])
            ->findOrFail($id);
    }

    public function accept(Post $post): bool
    {
        return $post->update([
            'status' => PostStatus::Approved->value,
            'accepted_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    public function reject(Post $post, ?string $reason = null): bool
    {
        return $post->update([
            'status' => PostStatus::Rejected->value,
            'rejection_reason' => $reason,
            'accepted_at' => null,
        ]);
    }

    protected function afterCreate(Post $post): void
    {
       $this->sendAdminNotification(__('New post'), __('A new post has been created'), [
            Action::make('view')
                ->url(route('filament.admin.resources.posts.view', $post->id))
                ->label(__('Let\'s review it'))
        ]);
    }
    
    public function listFeed($per_page=15)
    {
        return $this->post->newQuery()
            ->where('status', PostStatus::Approved->value)
            ->with(['author'])
            ->withCount(['comments', 'likes as likes_count' => function ($q) { $q->where('value', 1); }])
            ->latest('id')
            ->paginate($per_page);
    }
}


