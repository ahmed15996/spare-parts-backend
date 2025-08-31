<?php

namespace App\Http\Resources\API\V1;

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
           'brand_id'=>$this->brandModel->brand_id,
           'brand_model_id'=>$this->brandModel->id,
           'manufacture_year'=>$this->manufacture_year,
           'number'=>$this->number,
        ];
    }
}
