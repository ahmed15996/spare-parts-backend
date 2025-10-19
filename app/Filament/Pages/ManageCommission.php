<?php

namespace App\Filament\Pages;

use App\Settings\CommissionSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageCommission extends SettingsPage
{
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = CommissionSettings::class;
    protected static ?int $navigationSort = 1002;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                
                Forms\Components\TextInput::make('client_commission')
                    ->label('Client Commission')
                    ->translateLabel()
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%')
                    ->required(),
                Forms\Components\TextInput::make('provider_commission')
                    ->label('Provider Commission')
                    ->translateLabel()
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%')
                    ->required(),
                Forms\Components\TextInput::make('client_commission_text_ar')
                    ->label('Client Commission Text Ar')
                    ->translateLabel()
                    ->required(),
                Forms\Components\TextInput::make('client_commission_text_en')
                    ->label('Client Commission Text En')
                    ->translateLabel()
                    ->required(),
                Forms\Components\TextInput::make('provider_commission_text_ar')
                    ->label('Provider Commission Text Ar')
                    ->translateLabel()
                    ->required(),
                Forms\Components\TextInput::make('provider_commission_text_en')
                    ->label('Provider Commission Text En')
                    ->translateLabel()
                    ->required(),
            ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }
    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable{
        return __('Manage Commission');
    }

    public static function getNavigationLabel(): string
    {
        return __('Manage Commission');
    }
}
