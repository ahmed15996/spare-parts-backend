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
        $data =  [
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
        
        if($this->hasRole('provider')){
            $data['provider_id'] = $this->provider->id;
            $data['has_active_sub']= $this->provider->hasActiveSubscription();
        }else{
            $data['provider_id'] = null;
            $data['has_active_sub'] = null;
        }

        return $data;
    }
}
