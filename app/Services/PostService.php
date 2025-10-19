<?php

namespace App\Services;

use App\Models\Post;
use App\Enums\PostStatus;
use Filament\Notifications\Actions\Action;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\PostStatusNotification;

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
            ->find($id);
    }

    public function accept(Post $post): bool
    {
        $post = tap($post,function($post){
            $post->update([
                'status' => PostStatus::Approved->value,
                'accepted_at' => now(),
                'rejection_reason' => null,
            ]);
        });
        $this->afterUpdate($post,1);
        return true ;
    }

    public function reject(Post $post, ?string $reason = null): bool
    {
        $post = tap($post,function($post) use ($reason){
            $post->update([
            'status' => PostStatus::Rejected->value,
            'rejection_reason' => $reason,
            'accepted_at' => null,
        ]);
        });
        $this->afterUpdate($post,2);
        return true;
    }

    protected function afterCreate(Post $post): void
    {
       $this->sendAdminNotification(__('New post'), __('A new post has been created'), [
            Action::make('view')
                ->url(route('filament.admin.resources.posts.view', $post->id))
                ->label(__('Let\'s review it'))
        ]);
    }   

    protected function afterUpdate(Post $post ,int $type): void
    {
        //type 1 mean accept 
        //type 2 mean reject
        $recipent = $post->author;

        $rejection_title = [
            'en'=>'Your post has been rejected',
            'ar'=>'المنشور الخاص بك مرفوض',
        ];
        $rejection_body = [
            'en'=>'Your post has been rejected',
            'ar'=>'المنشور الخاص بك مرفوض',
        ];

        $acceptance_title = [   
            'en'=>'Your post has been accepted',
            'ar'=>'المنشور الخاص بك مقبول',
        ];
        $acceptance_body = [
            'en'=>'Your post has been accepted',
            'ar'=>'المنشور الخاص بك مقبول',
        ];
        $data = [
            'title'=>[
                'en'=>$type === 1 ? $acceptance_title['en'] : $rejection_title['en'],
                'ar'=>$type === 1 ? $acceptance_title['ar'] : $rejection_title['ar'],
            ],
            'body'=>[
                'en'=>$type === 1 ? $acceptance_body['en'] : $rejection_body['en'],
                'ar'=>$type === 1 ? $acceptance_body['ar'] : $rejection_body['ar'],
            ],
            'metadata'=>[
                'type'=>$type === 1 ? 'accepted' : 'rejected',
                'route'=>'posts.show',
                'post_id'=>$post->id,
            ]
        ];
        $recipent->notify(new PostStatusNotification($post,$data));
        $recipent->customNotifications()->create([
            'title' => $data['title'],
            'body' => $data['body'],
            'metadata' => $data['metadata'],
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


