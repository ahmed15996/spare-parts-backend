<?php

namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRequest extends EditRecord
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label(__('View Request')),
            Actions\DeleteAction::make()
                ->label(__('Delete Request')),
        ];
    }

    public function getTitle(): string
    {
        return __('Edit Request');
    }
}
