<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions;
class CreateCategory extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    protected static string $resource = CategoryResource::class;
    

    public function getTitle(): string
    {
        return __('Add Category');
    }

    public function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
