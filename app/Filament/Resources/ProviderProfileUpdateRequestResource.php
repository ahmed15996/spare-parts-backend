<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderProfileUpdateRequestResource\Pages;
use App\Models\ProviderProfileUpdateRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProviderProfileUpdateRequestResource extends Resource
{
    protected static ?string $model = ProviderProfileUpdateRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    
    protected static ?int $navigationSort = 2;
    
    public static function getNavigationGroup(): ?string
    {
        return __('Provider Requests');
    }
    
    public static function getModelLabel(): string
    {
        return __('Profile Update Request');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('Profile Update Requests');
    }
    
    public static function getNavigationLabel(): string
    {
        return __('Profile Updates');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::pending()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        // We don't need a form since we're not creating or editing
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->where('status', 0))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ID'))
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('provider.user.first_name')
                    ->label(__('Provider Name'))
                    ->formatStateUsing(fn ($record) => 
                        $record->provider?->user?->first_name . ' ' . $record->provider?->user?->last_name
                    )
                    ->searchable(['providers.user.first_name', 'providers.user.last_name']),
                    
                Tables\Columns\TextColumn::make('provider.user.phone')
                    ->label(__('Phone'))
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('store_name')
                    ->label(__('Requested Store Name'))
                    ->formatStateUsing(fn ($state) => $state['ar'] ?? '-')
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('city.name')
                    ->label(__('City'))
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Requested At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviderProfileUpdateRequests::route('/'),
            'view' => Pages\ViewProviderProfileUpdateRequest::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }
}
