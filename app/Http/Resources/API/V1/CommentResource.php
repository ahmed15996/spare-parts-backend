<?php

namespace App\Http\Resources\API\V1;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name,
                'avatar' => $this->author->role == 'client' ? $this->author->getFirstMediaUrl('avatar') : $this->author->getFirstMediaUrl('logo'),
            ],
            'created_at' => Carbon::parse($this->created_at)->format('H:i d/m/Y'),
        ];
    }
}


