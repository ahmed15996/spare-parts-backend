<?php

namespace App\Http\Resources\API\V1;

use App\Http\Resources\API\V1\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderBreadcramb extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'=>$this->store_name,
            'category'=> CategoryResource::make($this->category),
        ];
    }
}
