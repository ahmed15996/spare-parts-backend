<?php

namespace App\Http\Resources\API\V1\Provider;

use App\Http\Resources\API\V1\CarResource;
use App\Http\Resources\API\V1\OfferResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class UpdateDaysRequest extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

    }
}
