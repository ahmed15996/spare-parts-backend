<?php

namespace App\Filament\Resources\PackageResource\Pages;

use App\Filament\Resources\PackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPackages extends ListRecords
{
    use ListRecords\Concerns\Translatable;
    protected static string $resource = PackageResource::class;

    public function getTitle(): string
    {
        return __('Packages');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make()
                ->label(__('Create') . ' ' . __('Package')),
        ];
    }
}
