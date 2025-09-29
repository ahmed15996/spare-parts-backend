<?php

namespace App\Http\Resources\API\V1;

use App\Enums\Users\ProfileStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonalProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' =>  $this->hasRole('client') ? $this->getFirstMediaUrl('avatar') : $this->provider->getFirstMediaUrl('logo'),
            'address' => $this->address,
            'lat' => $this->lat,
            'long' => $this->long,
            'city_id' => $this->city->id,
            'role' => $this->roles->last()->name,
        ];
    }
}
