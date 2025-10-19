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
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading(fn (BrandModel $record) => $record->cars()->count() > 0 
                        ? __('Delete Brand Model and Associated Cars?')
                        : __('Delete Brand Model?'))
                    ->modalDescription(fn (BrandModel $record) => $record->cars()->count() > 0 
                        ? __('This brand model has :count associated car(s). Do you want to delete the brand model along with all associated cars?', ['count' => $record->cars()->count()])
                        : __('Are you sure you want to delete this brand model?'))
                    ->modalSubmitActionLabel(__('Yes, Delete'))
                    ->before(function (BrandModel $record) {
                        // Delete all associated cars if they exist
                        if ($record->cars()->count() > 0) {
                            $carsCount = $record->cars()->count();
                            $record->cars()->delete();
                            
                            \Filament\Notifications\Notification::make()
                                ->title(__('Cars Deleted'))
                                ->body(__(':count associated car(s) have been deleted.', ['count' => $carsCount]))
                                ->warning()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading(__('Delete Brand Models and Associated Cars?'))
                        ->modalDescription(function ($records) {
                            $totalCars = 0;
                            foreach ($records as $record) {
                                $totalCars += $record->cars()->count();
                            }
                            
                            if ($totalCars > 0) {
                                return __('The selected brand models have :count associated car(s) in total. Do you want to delete the brand models along with all associated cars?', ['count' => $totalCars]);
                            }
                            
                            return __('Are you sure you want to delete the selected brand models?');
                        })
                        ->modalSubmitActionLabel(__('Yes, Delete All'))
                        ->before(function ($records) {
                            // Delete all associated cars for each brand model
                            $totalCarsDeleted = 0;
                            foreach ($records as $record) {
                                if ($record->cars()->count() > 0) {
                                    $totalCarsDeleted += $record->cars()->count();
                                    $record->cars()->delete();
                                }
                            }
                            
                            if ($totalCarsDeleted > 0) {
                                \Filament\Notifications\Notification::make()
                                    ->title(__('Cars Deleted'))
                                    ->body(__(':count associated car(s) have been deleted.', ['count' => $totalCarsDeleted]))
                                    ->warning()
                                    ->send();
                            }
                        }),
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
