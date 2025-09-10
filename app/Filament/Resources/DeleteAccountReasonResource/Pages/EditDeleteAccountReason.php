<?php

namespace App\Filament\Resources\DeleteAccountReasonResource\Pages;

use App\Filament\Resources\DeleteAccountReasonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeleteAccountReason extends EditRecord
{
    protected static string $resource = DeleteAccountReasonResource::class;

    public function getTitle(): string
    {
        return __('Edit Delete Account Reason');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label(__('Delete')),
        ];
    }
}
