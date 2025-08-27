<?php

namespace App\Filament\Resources\DayResource\Pages;

use App\Filament\Resources\DayResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDay extends EditRecord
{
    use EditRecord\Concerns\Translatable;
    
    protected static string $resource = DayResource::class;

    public function getTitle(): string
    {
        return __('Edit') . ' ' . __('Day');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make()
                ->label(__('Delete')),
            Actions\LocaleSwitcher::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('Day') . ' ' . __('Record updated successfully');
    }
}
