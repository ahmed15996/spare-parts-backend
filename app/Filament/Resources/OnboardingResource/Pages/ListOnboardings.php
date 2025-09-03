<?php

namespace App\Filament\Resources\OnboardingResource\Pages;

use App\Filament\Resources\OnboardingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOnboardings extends ListRecords
{
    protected static string $resource = OnboardingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
