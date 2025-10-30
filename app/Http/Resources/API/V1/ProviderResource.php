<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\V1\ProductResource;
use App\Http\Resources\API\V1\BrandResource;
use App\Http\Resources\API\V1\ProviderDayResource;
use App\Http\Resources\API\V1\CategoryResource;
use Illuminate\Support\Facades\Auth;

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
                'phone' => $this->user->phone,
                'rating' => $this->getAverageRating(),
                'address' => $this->user->address,
                'category' => CategoryResource::make($this->category),
                'open_status' => $this->isCurrentlyOpen(),
                'license' => $this->commercial_number,

            ];

            if(Auth::check()){
                $data['is_favourite'] = $this->favourites->where('user_id', Auth::user()->id)->count() > 0 ? true : false;
            }else{
                $data['is_favourite'] = false;
            }

            if($request->route()->getName() == 'client.banners.show'){
                $data['days'] = ProviderDayResource::collection($this->days);
            }

            if($request->route()->getName() == 'client.providers.show'){
                $data['days'] = ProviderDayResource::collection($this->days);
                $data['brands'] = BrandResource::collection($this->brands->take(5));
                $data['banners'] = BannerResource::collection($this->activeProfileBanners);
                $data['products'] = ProductResource::collection($this->products->where('published', true)->take(5));
            }

            return $data;
        }
}
