<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DayResource\Pages;
use App\Filament\Resources\DayResource\RelationManagers;
use App\Models\Day;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Concerns\Translatable;

class DayResource extends Resource
{
    use Translatable;
    
    protected static ?string $model = Day::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    protected static ?string $modelLabel = 'Day';
    protected static ?string $pluralModelLabel = 'Days';
    
    public static function getModelLabel(): string
    {
        return __('Day');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('Days');
    }
    
    public static function getNavigationLabel(): string
    {
        return __('Days');
    }
    public static function getNavigationGroup(): ?string
    {
        return __('Selection Lists');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Day Information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                    ]),
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
                // Tables\Actions\DeleteAction::make()
                //     ->requiresConfirmation()
                //     ->modalHeading(fn (Day $record) => $record->providers()->count() > 0 
                //         ? __('Delete Day and Remove Provider Associations?')
                //         : __('Delete Day?'))
                //     ->modalDescription(fn (Day $record) => $record->providers()->count() > 0 
                //         ? __('This day is associated with :count provider(s). Do you want to delete the day and remove these associations?', ['count' => $record->providers()->count()])
                //         : __('Are you sure you want to delete this day?'))
                //     ->modalSubmitActionLabel(__('Yes, Delete'))
                //     ->before(function (Day $record) {
                //         // Detach all associated providers (remove pivot table records)
                //         if ($record->providers()->count() > 0) {
                //             $providersCount = $record->providers()->count();
                //             $record->providers()->detach();
                            
                //             \Filament\Notifications\Notification::make()
                //                 ->title(__('Provider Associations Removed'))
                //                 ->body(__(':count provider association(s) have been removed.', ['count' => $providersCount]))
                //                 ->warning()
                //                 ->send();
                //         }
                //     }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading(__('Delete Days and Remove Provider Associations?'))
                        ->modalDescription(function ($records) {
                            $totalProviders = 0;
                            foreach ($records as $record) {
                                $totalProviders += $record->providers()->count();
                            }
                            
                            if ($totalProviders > 0) {
                                return __('The selected days are associated with :count provider(s) in total. Do you want to delete the days and remove these associations?', ['count' => $totalProviders]);
                            }
                            
                            return __('Are you sure you want to delete the selected days?');
                        })
                        ->modalSubmitActionLabel(__('Yes, Delete All'))
                        ->before(function ($records) {
                            // Detach all associated providers for each day
                            $totalProvidersDetached = 0;
                            foreach ($records as $record) {
                                if ($record->providers()->count() > 0) {
                                    $totalProvidersDetached += $record->providers()->count();
                                    $record->providers()->detach();
                                }
                            }
                            
                            if ($totalProvidersDetached > 0) {
                                \Filament\Notifications\Notification::make()
                                    ->title(__('Provider Associations Removed'))
                                    ->body(__(':count provider association(s) have been removed.', ['count' => $totalProvidersDetached]))
                                    ->warning()
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->defaultSort('id', 'asc');
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
            'index' => Pages\ListDays::route('/'),
            'create' => Pages\CreateDay::route('/create'),
            'edit' => Pages\EditDay::route('/{record}/edit'),
        ];
    }
}
