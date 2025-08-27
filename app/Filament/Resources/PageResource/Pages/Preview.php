<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class Preview extends ViewRecord
{    use ViewRecord\Concerns\Translatable;
    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
    protected static string $resource = PageResource::class;

    public function getTitle(): string
    {
        return __('Preview');
    }
}
