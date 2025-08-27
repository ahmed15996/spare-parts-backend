<?php

namespace App\Filament\Resources\BrandModelResource\Pages;

use App\Filament\Resources\BrandModelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBrandModels extends ListRecords
{
    use ListRecords\Concerns\Translatable;
    
    protected static string $resource = BrandModelResource::class;

    public function getTitle(): string
    {
        return __('Brand Models');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make()
                ->label(__('Create') . ' ' . __('Brand Model')),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
