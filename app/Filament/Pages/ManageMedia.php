<?php

namespace App\Filament\Pages;

use App\Settings\MediaSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

    class ManageMedia extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = MediaSettings::class;

    protected static ?int $navigationSort = 1001;
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('linked_in')
                    ->label('Linked In')
                    ->translateLabel()
                    ->required(),
                Forms\Components\TextInput::make('facebook')
                    ->label('Facebook')
                    ->translateLabel()
                    
                    ->required(),
                Forms\Components\TextInput::make('twitter')
                    ->label('Twitter')
                    ->translateLabel()
                    
                    ->required(),
                Forms\Components\TextInput::make('tiktok')
                    ->label('Tiktok')
                    ->translateLabel()
                    
                    ->required(),
                Forms\Components\TextInput::make('instagram')
                    ->label('Instagram')
                    ->translateLabel()
                    
                    ->required(),
                Forms\Components\TextInput::make('snapchat')
                    ->label('Snapchat')
                    ->translateLabel()
                    
                    ->required(),
                Forms\Components\TextInput::make('app_store')
                    ->label('App Store')
                    ->translateLabel()
                    
                    ->required(),
                Forms\Components\TextInput::make('google_play')
                    ->label('Google Play')
                    ->translateLabel()
                    
                    ->required(),
                // ...
            ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }
    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable{
        return __('Manage Media');
    }

    public static function getNavigationLabel(): string
    {
        return __('Manage Media');
    }
}
