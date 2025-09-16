<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data= [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            
        ];

        if($this->discount_percentage){
            $data['discount_price'] = $this->price * (1 - $this->discount_percentage / 100);
        }
            if($request->route()->getName() == 'client.providers.show' || $request->route()->getName() == 'client.providers.products.index'){
                $data['main_image'] = $this->getFirstMediaUrl('products');
            }
            if($request->route()->getName() == 'client.providers.products.show'){
                $data['description'] = $this->description;
                $data['gallery'] = $this->getMedia('products')->map(function($media){
                   return $media->getUrl();
                })->toArray();
                $data['provider'] =[
                    'user_id' => $this->provider->user_id,
                    'phone' => $this->provider->user->phone,
                    'address' => $this->provider->address,
                    'name' => $this->provider->store_name,
                    'avatar' => $this->provider->getFirstMediaUrl('logo'),
                    'rating' => $this->provider->getAverageRating(),
                ];
            }
            return $data;
    }
}
