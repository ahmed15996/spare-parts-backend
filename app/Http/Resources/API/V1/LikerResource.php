<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class LikerResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;
        
        return [
            'id' => $this->id,
            'name' => $user->name ?? ($user->first_name . ' ' . $user->last_name ?? ''),
            'avatar' => $user->getFirstMediaUrl('avatar')
        ];
    }
}
