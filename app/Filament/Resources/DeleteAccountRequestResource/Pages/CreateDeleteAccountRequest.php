<?php

namespace App\Filament\Resources\DeleteAccountRequestResource\Pages;

use App\Filament\Resources\DeleteAccountRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDeleteAccountRequest extends CreateRecord
{
    protected static string $resource = DeleteAccountRequestResource::class;

    public function getTitle(): string
    {
        return __('Create Delete Account Request');
    }
}
