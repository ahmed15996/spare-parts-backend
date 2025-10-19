<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateSubscription extends CreateRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('Subscription Created'))
            ->body(__('The subscription has been created successfully.'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure end_date is properly formatted
        if (isset($data['end_date'])) {
            $data['end_date'] = \Carbon\Carbon::parse($data['end_date'])->format('Y-m-d');
        }

        return $data;
    }
}

