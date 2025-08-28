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
            return [
                'id' => $this->id,
                'title' => $this->title,
                'description' => $this->description,
                'type' => $this->type,
                'original_price' => $this->original_price,
                'discount_price' => $this->discount_price,
                'discount_percentage' => $this->discount_percentage,
                'image' => $this->getFirstMediaUrl('image'),
            ];
        }
}
