<?php

namespace App\Filament\Resources\ProviderRegistrationRequestResource\Pages;

use App\Filament\Resources\ProviderRegistrationRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProviderRegistrationRequest extends CreateRecord
{
    protected static string $resource = ProviderRegistrationRequestResource::class;

    public function getTitle(): string
    {
        return __('Create') . ' ' . __('Provider Registration Request');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('Provider Registration Request') . ' ' . __('Record created successfully');
    }
}
