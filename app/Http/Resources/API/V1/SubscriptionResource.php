<?php

namespace App\Http\Resources\API\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'package' => PackageResource::make($this->package),
            'end_date' => Carbon::parse($this->end_date)->translatedFormat('Y M d'),
            'is_active' => $this->is_active,
        ];
    }
}
