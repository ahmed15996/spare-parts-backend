<?php

namespace App\Filament\Resources\DeleteAccountRequestResource\Pages;

use App\Filament\Resources\DeleteAccountRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeleteAccountRequests extends ListRecords
{
    protected static string $resource = DeleteAccountRequestResource::class;

    public function getTitle(): string
    {
        return __('Delete Account Requests');
    }

}
