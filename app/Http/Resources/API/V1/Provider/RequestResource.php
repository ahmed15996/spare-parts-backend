<?php

namespace App\Http\Resources\API\V1\Provider;

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
            'description' => $this->description,
            'user'=>[
                'id' => $this->user->id,
                'name' => $this->user->first_name . ' ' . $this->user->last_name,
                'avatar' => $this->user->getFirstMediaUrl('avatar'),
            ]
        ];

        
        if($request->route()->getName() == 'provider.requests.show' || $request->route()->getName() == 'provider.offers.show'){
            $data['city'] = $this->city->name;
            $data['category'] = $this->category->name;
            $data['car_type'] = $this->car->brandModel->brand->name  . ' ' . $this->car->manufacture_year;
            $data['car'] = CarResource::make($this->car);
        }
        return $data;
    }
}
