<?php

namespace App\Filament\Resources\DeleteAccountRequestResource\Pages;

use App\Filament\Resources\DeleteAccountRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDeleteAccountRequest extends ViewRecord
{
    protected static string $resource = DeleteAccountRequestResource::class;

    public function getTitle(): string
    {
        return __('View Delete Account Request');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(__('Edit')),
        ];
    }
}
