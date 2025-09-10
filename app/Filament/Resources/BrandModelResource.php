<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandModelResource\Pages;
use App\Filament\Resources\BrandModelResource\RelationManagers;
use App\Models\BrandModel;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Concerns\Translatable;

class BrandModelResource extends Resource
{
    use Translatable;
    
    protected static ?string $model = BrandModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    
    protected static ?string $modelLabel = 'Brand Model';
    protected static ?string $pluralModelLabel = 'Brand Models';
    
    public static function getModelLabel(): string
    {
        return __('Brand Model');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('Brand Models');
    }
    
    public static function getNavigationLabel(): string
    {
        return __('Brand Models');
    }
    public static function getNavigationGroup(): ?string
    {
        return __('Selection Lists');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Brand Model Information'))
                    ->schema([
                        Forms\Components\Select::make('brand_id')
                            ->label(__('Brand'))
                            ->options(Brand::all()->mapWithKeys(function ($brand) {
                                $name = $brand->getTranslation('name', app()->getLocale()) ?? 
                                       $brand->getTranslation('name', 'en') ?? 
                                       $brand->getTranslation('name', 'ar') ?? 
                                       'Brand #' . $brand->id;
                                return [$brand->id => $name];
                            }))
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('brand.name')
                    ->label(__('Brand'))
                    ->formatStateUsing(fn ($record) => $record->brand ? $record->brand->getTranslation('name', app()->getLocale()) : '')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Model Name'))
                    ->searchable()
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
                Tables\Filters\SelectFilter::make('brand_id')
                    ->label(__('Brand'))
                    ->options(Brand::all()->mapWithKeys(function ($brand) {
                        $name = $brand->getTranslation('name', app()->getLocale()) ?? 
                               $brand->getTranslation('name', 'en') ?? 
                               $brand->getTranslation('name', 'ar') ?? 
                               'Brand #' . $brand->id;
                        return [$brand->id => $name];
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
            'index' => Pages\ListBrandModels::route('/'),
            'create' => Pages\CreateBrandModel::route('/create'),
            'edit' => Pages\EditBrandModel::route('/{record}/edit'),
        ];
    }
}
