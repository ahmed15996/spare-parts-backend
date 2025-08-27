<?php

namespace App\Filament\Resources\BrandResource\Pages;

use App\Filament\Resources\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBrand extends EditRecord
{
    use EditRecord\Concerns\Translatable;
    
    protected static string $resource = BrandResource::class;

    public function getTitle(): string
    {
        return __('Edit') . ' ' . __('Brand');
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
        return __('Brand') . ' ' . __('Record updated successfully');
    }
}
