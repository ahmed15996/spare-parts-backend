<?php

namespace App\Filament\Resources;

use App\Enums\AdminBannerType;
use App\Filament\Resources\AdminBannerResource\Pages;
use App\Models\AdminBanner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdminBannerResource extends Resource
{
    protected static ?string $model = AdminBanner::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('Informative Content');
    }

    public static function getNavigationLabel(): string
    {
        return __('Admin Banners');
    }

    public static function getModelLabel(): string
    {
        return __('Admin Banner');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Admin Banners');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('Banner Information'))
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label(__('Title'))
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('link')
                        ->label(__('Link'))
                        ->url()
                        ->required()
                        ->maxLength(2048),

                    Forms\Components\Toggle::make('is_active')
                        ->label(__('Active'))
                        ->default(true),

                    Forms\Components\Hidden::make('type')
                        ->default(AdminBannerType::Admin->value),
                ])
                ->columns(2),

            Forms\Components\Section::make(__('Media'))
                ->schema([
                    Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                        ->label(__('Banner Image'))
                        ->collection('image')
                        ->image()
                        ->imageEditor()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(2048)
                        ->required(fn (string $operation): bool => $operation === 'create'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('Image'))
                    ->collection('image')
                    ->size(50),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('link')
                    ->label(__('Link'))
                    ->limit(40)
                    ->url(fn (AdminBanner $record): string => $record->link, true),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdminBanners::route('/'),
            'create' => Pages\CreateAdminBanner::route('/create'),
            'edit' => Pages\EditAdminBanner::route('/{record}/edit'),
        ];
    }
}
