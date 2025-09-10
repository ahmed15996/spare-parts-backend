<?php

namespace App\Filament\Resources\DeleteAccountReasonResource\Pages;

use App\Filament\Resources\DeleteAccountReasonResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDeleteAccountReason extends CreateRecord
{
    protected static string $resource = DeleteAccountReasonResource::class;

    public function getTitle(): string
    {
        return __('Create Delete Account Reason');
    }
}
