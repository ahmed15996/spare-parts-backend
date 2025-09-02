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
            'user' => [
                'id' =>$this->request->user_id,
                'name' => $this->request->user->first_name . ' ' . $this->request->user->last_name,
                'avatar' => $this->request->user->getFirstMediaUrl('avatar'),
            ] 
        ];

        if($request->route()->getName() == 'provider.offers.show'){
            unset($data['user']);
           $data['description'] = $this->description;
           $data['has_delivery'] = $this->has_delivery;
           $data['request']= ProviderRequestResource::make($this->request);
        }
        return $data;
    }
}
