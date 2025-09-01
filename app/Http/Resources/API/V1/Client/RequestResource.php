<?php

namespace App\Http\Resources\API\V1\Client;

use App\Http\Resources\API\V1\CarResource;
use App\Http\Resources\API\V1\OfferResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class RequestResource extends JsonResource
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
            'category'=> $this->category->name,
            'city' => $this->city->name,
            'car_type'=> $this->car->brandModel->brand->name  . ' ' . $this->car->manufacture_year
        ];

        
        if($request->route()->getName() == 'client.requests.show'){
            $data['description'] = $this->description;
            $data['car'] = CarResource::make($this->car);
            $data['offers'] = OfferResource::collection($this->offers);

        }
        return $data;
    }
}
