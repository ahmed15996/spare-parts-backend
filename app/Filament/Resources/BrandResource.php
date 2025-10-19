<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Filament\Resources\BrandResource\RelationManagers;
use App\Models\Brand;
use App\Models\BrandModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Concerns\Translatable;

class BrandResource extends Resource
{
    use Translatable;
    
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    
    protected static ?string $modelLabel = 'Brand';
    protected static ?string $pluralModelLabel = 'Brands';
    
    public static function getModelLabel(): string
    {
        return __('Brand');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('Brands');
    }
    
    public static function getNavigationLabel(): string
    {
        return __('Brands');
    }
    public static function getNavigationGroup(): ?string
    {
        return __('Selection Lists');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Brand Information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                    ]),
                
                Forms\Components\Section::make(__('Logo'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->label(__('Logo'))
                            ->collection('logo')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048)
                            ->directory('brands/logos')
                            ->visibility('public'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('logo')
                    ->label(__('Logo'))
                    ->collection('logo')
                    ->width(60)
                    ->height(60),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('models_count')
                    ->label(__('Models Count'))
                    ->counts('models')
                    ->badge(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading(fn (Brand $record) => $record->models()->count() > 0 
                        ? __('Delete Brand and Associated Models?')
                        : __('Delete Brand?'))
                    ->modalDescription(function (Brand $record) {
                        $modelsCount = $record->models()->count();
                        if ($modelsCount > 0) {
                            // Count total cars across all models
                            $carsCount = 0;
                            foreach ($record->models as $model) {
                                $carsCount += $model->cars()->count();
                            }
                            
                            if ($carsCount > 0) {
                                return __('This brand has :models_count model(s) with :cars_count associated car(s). Do you want to delete the brand along with all models and cars?', [
                                    'models_count' => $modelsCount,
                                    'cars_count' => $carsCount
                                ]);
                            }
                            
                            return __('This brand has :count model(s). Do you want to delete the brand along with all models?', ['count' => $modelsCount]);
                        }
                        
                        return __('Are you sure you want to delete this brand?');
                    })
                    ->modalSubmitActionLabel(__('Yes, Delete'))
                    ->before(function (Brand $record) {
                        // Delete all associated models and their cars
                        if ($record->models()->count() > 0) {
                            $modelsCount = $record->models()->count();
                            $carsCount = 0;
                            
                            // Delete cars for each model, then delete the model
                            foreach ($record->models as $model) {
                                $carsCount += $model->cars()->count();
                                $model->cars()->delete();
                            }
                            
                            // Delete all models
                            $record->models()->delete();
                            
                            // Show notification
                            $message = $carsCount > 0 
                                ? __(':models_count model(s) and :cars_count car(s) have been deleted.', [
                                    'models_count' => $modelsCount,
                                    'cars_count' => $carsCount
                                ])
                                : __(':count model(s) have been deleted.', ['count' => $modelsCount]);
                            
                            \Filament\Notifications\Notification::make()
                                ->title(__('Models Deleted'))
                                ->body($message)
                                ->warning()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading(__('Delete Brands and Associated Models?'))
                        ->modalDescription(function ($records) {
                            $totalModels = 0;
                            $totalCars = 0;
                            
                            foreach ($records as $record) {
                                $totalModels += $record->models()->count();
                                foreach ($record->models as $model) {
                                    $totalCars += $model->cars()->count();
                                }
                            }
                            
                            if ($totalModels > 0) {
                                if ($totalCars > 0) {
                                    return __('The selected brands have :models_count model(s) with :cars_count car(s) in total. Do you want to delete the brands along with all models and cars?', [
                                        'models_count' => $totalModels,
                                        'cars_count' => $totalCars
                                    ]);
                                }
                                
                                return __('The selected brands have :count model(s) in total. Do you want to delete the brands along with all models?', ['count' => $totalModels]);
                            }
                            
                            return __('Are you sure you want to delete the selected brands?');
                        })
                        ->modalSubmitActionLabel(__('Yes, Delete All'))
                        ->before(function ($records) {
                            $totalModelsDeleted = 0;
                            $totalCarsDeleted = 0;
                            
                            // Delete all associated models and cars for each brand
                            foreach ($records as $record) {
                                if ($record->models()->count() > 0) {
                                    foreach ($record->models as $model) {
                                        $totalCarsDeleted += $model->cars()->count();
                                        $model->cars()->delete();
                                    }
                                    
                                    $totalModelsDeleted += $record->models()->count();
                                    $record->models()->delete();
                                }
                            }
                            
                            // Show notification if anything was deleted
                            if ($totalModelsDeleted > 0) {
                                $message = $totalCarsDeleted > 0 
                                    ? __(':models_count model(s) and :cars_count car(s) have been deleted.', [
                                        'models_count' => $totalModelsDeleted,
                                        'cars_count' => $totalCarsDeleted
                                    ])
                                    : __(':count model(s) have been deleted.', ['count' => $totalModelsDeleted]);
                                
                                \Filament\Notifications\Notification::make()
                                    ->title(__('Models Deleted'))
                                    ->body($message)
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
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
