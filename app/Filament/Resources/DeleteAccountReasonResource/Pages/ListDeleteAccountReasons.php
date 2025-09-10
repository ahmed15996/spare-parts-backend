<?php

namespace App\Filament\Resources\DeleteAccountReasonResource\Pages;

use App\Filament\Resources\DeleteAccountReasonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeleteAccountReasons extends ListRecords
{
    protected static string $resource = DeleteAccountReasonResource::class;

    public function getTitle(): string
    {
        return __('Delete Account Reasons');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Create')),
        ];
    }
}
