<?php

namespace App\Http\Resources\API\V1\Provider\Offers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\V1\Provider\RequestResource as ProviderRequestResource;
class ProviderOfferResource extends JsonResource
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
            'request_number' => $this->request->number,
            'price' => $this->price,
            'city' => $this->city->name,
            'description' => $this->description,
            'has_delivery' => $this->has_delivery,
            'request' => ProviderRequestResource::make($this->request),
            'user' => [
                'id' =>$this->request->user_id,
                'name' => $this->request->user->first_name . ' ' . $this->request->user->last_name,
                'avatar' => $this->request->user->getFirstMediaUrl('avatar'),
            ] 
        ];

        if($request->route()->getName() == 'provider.offers.show'){
            unset($data['user']);
        }
        return $data;
    }
}
