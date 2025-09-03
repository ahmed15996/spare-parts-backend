<?php

namespace App\Filament\Resources\OnboardingResource\Pages;

use App\Filament\Resources\OnboardingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOnboarding extends EditRecord
{
    protected static string $resource = OnboardingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
