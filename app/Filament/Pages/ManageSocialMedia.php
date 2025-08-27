<?php

namespace App\Filament\Pages;

use App\Settings\SocialMediaSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageSocialMedia extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-share';

    protected static string $settings = SocialMediaSettings::class;
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 2000;
    protected static ?string $navigationLabel = 'Manage Social Media';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Manage Social Media'))
                
                    ->schema([
                        Forms\Components\TextInput::make('facebook')
                            ->label('Facebook')
                            ->translateLabel()

                            ->required()
                            ->url(),
                        Forms\Components\TextInput::make('twitter')
                            ->label('Twitter')
                            ->translateLabel()
                            ->required()
                            ->url(),
                        Forms\Components\TextInput::make('instagram')
                            ->label('Instagram')
                            ->translateLabel()
                            ->required()
                            ->url(),
                        Forms\Components\TextInput::make('linkedin')
                            ->label('Linkedin')
                            ->translateLabel()
                            ->required()
                            ->url(),
                        Forms\Components\TextInput::make('youtube')
                            ->label('Youtube')
                            ->translateLabel()
                            ->required()
                            ->url(),
                    ]),
            ])
            ;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Manage Social Media');
    }

    public  function getTitle(): string
    {
        return __('Manage Social Media');
    }
    
    
}
