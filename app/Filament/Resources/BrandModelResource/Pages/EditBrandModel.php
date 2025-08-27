<?php

namespace App\Filament\Resources\BrandModelResource\Pages;

use App\Filament\Resources\BrandModelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBrandModel extends EditRecord
{
    use EditRecord\Concerns\Translatable;
    
    protected static string $resource = BrandModelResource::class;

    public function getTitle(): string
    {
        return __('Edit') . ' ' . __('Brand Model');
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
        return __('Brand Model') . ' ' . __('Record updated successfully');
    }
}
