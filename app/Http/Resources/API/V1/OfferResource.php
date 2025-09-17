<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $routeName = $request->route()?->getName();

        $data = [
            'id' => $this->id,
            'status' => $this->status,
            'price' => $this->price,
            'city' => $this->city?->name,
            'user_id' => $this->provider?->user_id,// id of provider user for chat 
            'provider' => [
                'name' => $this->provider?->store_name,
                'avatar' => $this->provider?->getFirstMediaUrl('logo') ,
                'user_id' => $this->provider?->user_id,
                
            ]
        ];

        if($routeName === 'client.requests.offers.show'){
            $data['provider_id'] = $this->provider_id;
            $data['description'] = $this->description;
            $data['has_delivery'] = $this->has_delivery;
        }

        return $data;
    }
}
