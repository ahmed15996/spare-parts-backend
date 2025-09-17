<?php

namespace App\Http\Resources\API\V1;

use App\Http\Resources\API\V1\BrandModelResource;
use App\Http\Resources\API\V1\BrandResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
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
                'brand'=>BrandResource::make($this->brand),
                'model'=> BrandModelResource::make($this->brandModel),
                'manufacture_year'=>$this->manufacture_year??null,
                'number'=>$this->number??null,
            ];

    }
}
