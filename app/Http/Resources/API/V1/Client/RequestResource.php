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
            'car' => CarResource::make($this->car)
        ];

        if($request && $request->route() && ($request->route()->getName() == 'api.conversations.conversation.messages') || ($request->route()->getName() == 'api.conversations.index')){
            unset($data);
            $data['number'] = $this->number;
            $data['description'] = $this->description;
        }

        
        if($request && $request->route() && $request->route()->getName() == 'client.requests.show'){
            $data['description'] = $this->description;
            $data['offers'] = OfferResource::collection($this->offers);

        }
        return $data;
    }
}
