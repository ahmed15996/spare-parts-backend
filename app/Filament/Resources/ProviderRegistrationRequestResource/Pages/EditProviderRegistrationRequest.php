<?php

namespace App\Filament\Resources\ProviderRegistrationRequestResource\Pages;

use App\Filament\Resources\ProviderRegistrationRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProviderRegistrationRequest extends EditRecord
{
    protected static string $resource = ProviderRegistrationRequestResource::class;

    public function getTitle(): string
    {
        return __('Edit') . ' ' . __('Provider Registration Request');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label(__('Delete')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('Provider Registration Request') . ' ' . __('Record updated successfully');
    }
}
