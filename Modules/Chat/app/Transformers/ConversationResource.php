<?php

namespace Modules\Chat\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $authUserId = Auth::id();

        $data=  [
            'id' => $this->id,
        ];

        return $data;
    }
}
