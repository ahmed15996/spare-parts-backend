<?php

namespace App\Filament\Resources\ProviderRegistrationRequestResource\Pages;

use App\Filament\Resources\ProviderRegistrationRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProviderRegistrationRequests extends ListRecords
{
    protected static string $resource = ProviderRegistrationRequestResource::class;

    public function getTitle(): string
    {
        return __('Provider Requests');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Create') . ' ' . __('Provider Registration Request')),
        ];
    }
}
