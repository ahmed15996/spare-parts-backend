<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $blocked = $this->blocked;
        $blockedData = [
            'id' => $blocked->id ?? null,
        ];

        if ($blocked && method_exists($blocked, 'hasRole')) {
            if ($blocked->hasRole('provider')) {
                $blockedData['store_name'] = $blocked->provider ? $blocked->provider->store_name : null;
                $blockedData['logo'] = $blocked->provider && method_exists($blocked->provider, 'getFirstMediaUrl') ? $blocked->provider->getFirstMediaUrl('logo') : null;
            } else {
                $blockedData['name'] = trim($blocked->first_name . ' ' . $blocked->last_name);
                $blockedData['avatar'] = method_exists($blocked, 'getFirstMediaUrl') ? $blocked->getFirstMediaUrl('avatar') : null;
            }
        }

        return [
            'id' => $this->id,
            'blocked_id' => $this->blocked_id,
            'blocked_user' => $blockedData,
        ];
    }
}
