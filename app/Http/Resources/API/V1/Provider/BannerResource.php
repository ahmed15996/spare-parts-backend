<?php

namespace App\Http\Resources\API\V1\Provider;

use App\Enums\BannerStatus;
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
            'number' => $this->number,
            'description' => $this->description,
            'status' => [
                'id' => $this->status->id,
                'label' => $this->status->label(),
            ],
        ];

       if($request->route()->getName() == 'provider.banners.show'){
        $data['title'] = $this->title;
        $data['image']=$this->getFirstMediaUrl('image');
        $data['original_price'] = $this->original_price;
        $data['discount_price'] = $this->discount_price;
        $data['discount_percentage'] = $this->discount_percentage;
        $data['rejection_reason'] = $this->rejection_reason ?? null;
       }
        return $data;
    }
}
