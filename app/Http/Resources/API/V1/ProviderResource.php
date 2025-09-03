<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\V1\ProductResource;
use App\Http\Resources\API\V1\BrandResource;
use App\Http\Resources\API\V1\ProviderDayResource;

class ProviderResource extends JsonResource
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
                'user_id' => $this->user_id,
                'store_name' => $this->store_name,
                'logo' => $this->getFirstMediaUrl('logo'),
                'rating' => $this->getAverageRating(),
                 
            ];

            if($request->route()->getName() == 'client.providers.show'){
                $data['days'] = ProviderDayResource::collection($this->days);
                $data['brands'] = BrandResource::collection($this->brands->take(5));
                $data['banners'] = BannerResource::collection($this->activeProfileBanners);
                $data['products'] = ProductResource::collection($this->products->take(5));
                $data['is_currently_open'] = $this->isCurrentlyOpen();
            }

            return $data;
        }
}
