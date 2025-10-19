<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditSubscription extends EditRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('Subscription Updated'))
            ->body(__('The subscription has been updated successfully.'));
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure end_date is properly formatted
        if (isset($data['end_date'])) {
            $data['end_date'] = \Carbon\Carbon::parse($data['end_date'])->format('Y-m-d');
        }

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Handle end_date when loading the form
        if (isset($data['end_date'])) {
            try {
                $data['end_date'] = \Carbon\Carbon::parse($data['end_date'])->format('Y-m-d');
            } catch (\Exception $e) {
                // Keep original value if parsing fails
            }
        }

        return $data;
    }
}

