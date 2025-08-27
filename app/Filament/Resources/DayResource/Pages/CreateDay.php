<?php

namespace App\Filament\Resources\DayResource\Pages;

use App\Filament\Resources\DayResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDay extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    
    protected static string $resource = DayResource::class;

    public function getTitle(): string
    {
        return __('Create') . ' ' . __('Day');
    }

    public function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('Day') . ' ' . __('Record created successfully');
    }
}
