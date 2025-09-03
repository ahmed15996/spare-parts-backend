<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\V1\ProductResource;
use App\Http\Resources\API\V1\BrandResource;
use App\Http\Resources\API\V1\ProviderDayResource;

class ProviderProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
            $data = [
                 'store_name' =>[
                    'ar' => $this->getTranslation('store_name', 'ar'),
                    'en' => $this->getTranslation('store_name', 'en'),
                 ],
                 'description' => $this->description,
                 'city_id' => $this->city_id,
                 'category_id' => $this->category_id,
                 'commercial_number' => $this->commercial_number,
                 'location' => $this->location,
                  'logo' => $this->getFirstMediaUrl('logo'),
                 'commercial_number_image' => $this->getFirstMediaUrl('commercial_number_image'),
                 'lat' => $this->user->lat,
                 'long' => $this->user->long,
                 'address' => $this->user->address,
                 
            ];

            return $data;
        }
}
