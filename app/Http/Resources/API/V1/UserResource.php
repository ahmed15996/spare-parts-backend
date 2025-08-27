<?php

namespace App\Http\Resources\API\V1;

use App\Enums\Users\ProfileStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $role = $this->roles->first();
        $data = [];
        $data['role'] = $role->name;
        if($role->name == 'client'){
            return [
                'id' => $this->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'address'=> $this->address,
                'avatar' => $this->getFirstMediaUrl('avatar'),
            ];
        }
        if($role->name == 'provider'){
            return [
                'user_id' => $this->id,
                'id' => $this->provider->id,
                'store_name' => $this->provider->store_name,
                'address' => $this->provider->address,
                'avatar' => $this->getFirstMediaUrl('avatar'),


            ];
        }
        return $data;
    }
}
