<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageGeneral extends SettingsPage
{
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GeneralSettings::class;
    protected static ?int $navigationSort =1000;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Forms\Components\TextInput::make('packages_discount')
                    ->label('Packages Discount')
                    ->translateLabel()
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%')
                    ->required(),
                
            ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }
    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable{
        return __('Manage General');
    }

    public static function getNavigationLabel(): string
    {
        return __('Manage General');
    }
}
