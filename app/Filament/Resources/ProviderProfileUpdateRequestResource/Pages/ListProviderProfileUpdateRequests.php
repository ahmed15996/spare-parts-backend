<?php

namespace App\Filament\Resources\ProviderProfileUpdateRequestResource\Pages;

use App\Filament\Resources\ProviderProfileUpdateRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProviderProfileUpdateRequests extends ListRecords
{
    protected static string $resource = ProviderProfileUpdateRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return __('Pending Profile Update Requests');
    }

    public function getSubheading(): ?string
    {
        $pendingCount = $this->getTableQuery()->where('status', 0)->count();
        return $pendingCount > 0 
            ? __(':count pending request(s) to review', ['count' => $pendingCount])
            : __('No pending requests');
    }
}
