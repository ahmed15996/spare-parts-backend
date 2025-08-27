<?php

namespace App\Filament\Resources\BrandResource\Pages;

use App\Filament\Resources\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBrand extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    
    protected static string $resource = BrandResource::class;

    public function getTitle(): string
    {
        return __('Create') . ' ' . __('Brand');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    public function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('Brand') . ' ' . __('Record created successfully');
    }
}
