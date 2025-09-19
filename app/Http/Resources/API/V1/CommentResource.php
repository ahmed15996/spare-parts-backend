<?php

namespace App\Http\Resources\API\V1;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $request->user();
        $isAuthor = $this->author_id === $user->getKey() && $this->author_type === get_class($user);
        $role = $this->author->roles->last();
        return [
            'id' => $this->id,
            'content' => $this->content,
            'is_author' => $isAuthor,
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name,
                'avatar' => $role->name == 'client' ? $this->author->getFirstMediaUrl('avatar') : $this->author->getFirstMediaUrl('logo'),
            ],
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
        ];
    }
}


