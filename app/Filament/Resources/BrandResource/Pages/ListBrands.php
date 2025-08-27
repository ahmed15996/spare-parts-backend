<?php

namespace App\Filament\Resources\BrandResource\Pages;

use App\Filament\Resources\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBrands extends ListRecords
{
    use ListRecords\Concerns\Translatable;
    
    protected static string $resource = BrandResource::class;

    public function getTitle(): string
    {
        return __('Brands');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Create') . ' ' . __('Brand'))
            ,
            Actions\LocaleSwitcher::make(),
        ];
    }
}
