<?php

namespace App\Filament\Resources\AdminBannerResource\Pages;

use App\Filament\Resources\AdminBannerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminBanner extends EditRecord
{
    protected static string $resource = AdminBannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
