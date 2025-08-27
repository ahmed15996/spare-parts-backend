<?php

namespace App\Filament\Resources\DayResource\Pages;

use App\Filament\Resources\DayResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDays extends ListRecords
{
    use ListRecords\Concerns\Translatable;
    
    protected static string $resource = DayResource::class;

    public function getTitle(): string
    {
        return __('Days');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            // Actions\CreateAction::make()
            //     ->label(__('Create') . ' ' . __('Day')),
            // Actions\LocaleSwitcher::make(),
        ];
    }
}
