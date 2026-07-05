<?php

namespace App\Filament\Resources\AdminBannerResource\Pages;

use App\Filament\Resources\AdminBannerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminBanners extends ListRecords
{
    protected static string $resource = AdminBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
