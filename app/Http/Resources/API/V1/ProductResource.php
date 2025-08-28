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
            'discount_price' => $this->discount_price,
            
        ];

            if($request->route()->getName() == 'client.providers.show'){
                $data['main_image'] = $this->getFirstMediaUrl('products');
            }
            if($request->route()->getName() == 'client.providers.products.show'){
                $data['description'] = $this->description;
                $data['gallery'] = $this->getMedia('products')->map(function($media){
                    return [
                        'url' => $media->getUrl(),
                    ];
                });
            }
            return $data;
    }
}
