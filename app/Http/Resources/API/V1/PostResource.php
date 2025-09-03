<?php

namespace App\Http\Resources\API\V1;

use App\Enums\PostStatus;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $request->user();

        // Normalize status to an int value compatible with PostStatus (handle legacy 0)
        $statusValue = is_int($this->status) ? $this->status : (int) $this->status;
        if ($statusValue === 0) {
            $statusValue = PostStatus::Pending->value;
        }
        $isApproved = $statusValue === PostStatus::Approved->value;
        $isRejected = $statusValue === PostStatus::Rejected->value;

        $isOwner = false;
        if ($user) {
            $isOwner = ($this->author_id === $user->getKey()) && ($this->author_type === get_class($user));
        }

        $data = [
            'id' => $this->id,
            'content' => $this->content,
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'status' => $statusValue,
        ];

        // Owner-specific rules
        if ($isOwner) {
            if ($isRejected) {
                // Only base info + rejection reason
                $data['rejection_reason'] = $this->rejection_reason;
                // No comments/media block for rejected own posts
            } else {
                // Accepted (or pending) => show normally
                $data['likes_count'] = $this->likes_count ?? 0;
                $data['comments_count'] = $this->whenCounted('comments');
                if ($request->route()?->getName() === 'posts.show' || $request->route()?->getName() === 'client.posts.show') {
                    $data['comments'] = CommentResource::collection($this->whenLoaded('comments'));
                }
                if ($this->hasMedia('posts')) {
                    $data['media'] = $this->getMedia('posts')->map(function ($media) {
                        return $media->getUrl();
                    })->toArray();
                }
            }
        } else {
            // Not owner: return as if accepted (full view)
            $data['likes_count'] = $this->likes_count ?? 0;
            $data['comments_count'] = $this->whenCounted('comments');
            if ($request->route()?->getName() === 'posts.show' || $request->route()?->getName() === 'client.posts.show') {
                $data['comments'] = CommentResource::collection($this->whenLoaded('comments'));
            }
            if ($this->hasMedia('posts')) {
                $data['media'] = $this->getMedia('posts')->map(function ($media) {
                    return $media->getUrl();
                })->toArray();
            }
        }

        // Author block (safe)
        $author = $this->author;
        if ($author) {
            $role = method_exists($author, 'roles') ? optional($author->roles)->where('guard_name', 'sanctum')->first() : null;
            $roleName = $role->name ?? null;

            if ($roleName === 'client') {
                $data['author'] = [
                    'id' => $author->id,
                    'name' => trim(($author->first_name ?? '') . ' ' . ($author->last_name ?? '')),
                    'address' => $author->address ?? null,
                    'avatar' => method_exists($author, 'getFirstMediaUrl') ? $author->getFirstMediaUrl('avatar') : null,
                ];
            } elseif ($roleName === 'provider') {
                $data['author'] = [
                    'id' => $author->id,
                    'name' => $author->name ?? ($author->store_name ?? null),
                    'address' => $author->address ?? null,
                    'avatar' => method_exists($author, 'getFirstMediaUrl') ? $author->getFirstMediaUrl('logo') : null,
                ];
            }
        }

        return $data;
    }
}


