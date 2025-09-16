<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderDayResource extends JsonResource
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
            'name' => $this->day->name,
            'is_closed' => $this->is_closed,
        ];

        if($this->is_closed){
            $data['from'] = null;
            $data['to'] = null;
        }

        return $data;
    }
}
