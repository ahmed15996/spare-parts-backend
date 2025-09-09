<?php

namespace App\Filament\Resources\OfferResource\Pages;

use App\Filament\Resources\OfferResource;
use Filament\Resources\Pages\ViewRecord;

class ViewOffer extends ViewRecord
{
    
    protected static string $resource = OfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No actions for read-only resource
        ];
    }

    public function getTitle(): string
    {
        return __('View Offer');
    }

    public function getBreadcrumbs(): array
    {
        return [
            // Remove breadcrumbs to avoid index route reference
        ];
    }
}
