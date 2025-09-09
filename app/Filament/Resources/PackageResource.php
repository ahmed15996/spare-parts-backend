<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Filament\Resources\PackageResource\RelationManagers;
use App\Models\Package;
use App\Enums\BannerType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Concerns\Translatable;

class PackageResource extends Resource
{
    use Translatable;
    
    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    
    protected static ?string $modelLabel = 'Package';
    protected static ?string $pluralModelLabel = 'Packages';
    
    public static function getModelLabel(): string
    {
        return __('Package');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('Packages');
    }
    
    public static function getNavigationLabel(): string
    {
        return __('Packages');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Package Information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label(__('Description'))
                            ->required()
                            ->rows(3),
                    ])->columns(2),
                
                Forms\Components\Section::make(__('Package Details'))
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label(__('Price'))
                            ->required()
                            ->numeric()
                            ->prefix('SAR')
                            ->minValue(0),
                        Forms\Components\TextInput::make('discount')
                            ->label(__('Discount'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix('%'),
                        Forms\Components\Select::make('banner_type')
                            ->label(__('Banner Type'))
                            ->options(collect(BannerType::cases())->mapWithKeys(function ($case) {
                                return [$case->value => $case->label()];
                            }))
                            ->required(),
                        Forms\Components\TextInput::make('duration')
                            ->label(__('Duration (Days)'))
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->suffix(__('days')),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('Price'))
                    ->money('SAR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('banner_type')
                    ->label(__('Banner Type'))
                    ->formatStateUsing(fn ($record) => $record->banner_type?->label() ?? '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label(__('Duration (Days)'))
                    ->formatStateUsing(fn ($record) => $record->duration)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('banner_type')
                    ->label(__('Banner Type'))
                    ->options(collect(BannerType::cases())->mapWithKeys(function ($case) {
                        return [$case->value => $case->label()];
                    }))
            ])
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}
