<?php

namespace App\Http\Resources\API\V1\Provider;

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
        return [
            'day_id' => $this->day_id,
            'day_name' => $this->day->name,
            'is_closed' => $this->is_closed,
            'from' => $this->from ? date('H:i A', strtotime($this->from)) : null,
            'to' => $this->to ? date('H:i A', strtotime($this->to)) : null,
        ];
    }
}
