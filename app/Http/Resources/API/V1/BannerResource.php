<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
            $data = [
                'id' => $this->id,
                'title' => $this->title,
                'type' => $this->type,
                'discount_percentage' => $this->discount_percentage,
                'image' => $this->getFirstMediaUrl('image'),
            ];

            if($request->route()->getName() == 'client.banners.show'){
                $data['description'] = $this->description;
                $data['original_price'] = $this->original_price;
                $data['discount_price'] = $this->discount_price;
                $data['provider'] =[
                    'user_id' => $this->provider->user_id,
                    'phone' => $this->provider->user->phone,
                    'address' => $this->provider->address,
                    'name' => $this->provider->store_name,
                    'avatar' => $this->provider->getFirstMediaUrl('logo'),
                    'rating' => $this->provider->getAverageRating(),
                    'days' => ProviderDayResource::collection($this->provider->days),
                ];
            }

            return $data;
        }
}
