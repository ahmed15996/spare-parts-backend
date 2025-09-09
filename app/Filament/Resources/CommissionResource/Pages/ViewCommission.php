<?php

namespace App\Filament\Resources\CommissionResource\Pages;

use App\Filament\Resources\CommissionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCommission extends ViewRecord
{
    protected static string $resource = CommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No actions for read-only resource
        ];
    }

    public function getTitle(): string
    {
        return __('View Commission');
    }

    protected function resolveRecord($key): \Illuminate\Database\Eloquent\Model
    {
        return static::getResource()::resolveRecordRouteBinding($key)->load([
            'user.provider',
            'items.product'
        ]);
    }
}
