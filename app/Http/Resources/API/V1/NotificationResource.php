<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'title' => $this->title,
            'body' => $this->body,
            'metadata' => $this->metadata ==[] ?null : $this->metadata  ,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at->format('H:i A'),
        ];
    }
}
