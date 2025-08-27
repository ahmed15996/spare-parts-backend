<?php

namespace App\Filament\Resources\BrandModelResource\Pages;

use App\Filament\Resources\BrandModelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBrandModel extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    
    protected static string $resource = BrandModelResource::class;

    public function getTitle(): string
    {
        return __('Create') . ' ' . __('Brand Model');
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
        return __('Brand Model') . ' ' . __('Record created successfully');
    }
}
