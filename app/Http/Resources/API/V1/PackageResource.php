<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function App\Helpers\setting;

class PackageResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'banner_type' => $this->banner_type->label(),
            'discount' => $this->discount? $this->discount :setting('general', 'packages_discount')??0,
        ];
    }
}
