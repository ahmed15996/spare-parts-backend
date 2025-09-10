<?php

namespace App\Filament\Resources\DeleteAccountRequestResource\Pages;

use App\Filament\Resources\DeleteAccountRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeleteAccountRequest extends EditRecord
{
    protected static string $resource = DeleteAccountRequestResource::class;

    public function getTitle(): string
    {
        return __('Edit Delete Account Request');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label(__('View')),
            Actions\DeleteAction::make()
                ->label(__('Delete')),
        ];
    }
}
