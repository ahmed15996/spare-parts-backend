<?php

namespace App\Filament\Resources\RequestResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OffersRelationManager extends RelationManager
{
    protected static string $relationship = 'offers';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Offers');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('provider.user.first_name')
                    ->label(__('Provider'))
                    ->formatStateUsing(fn ($record) => optional($record->provider?->user)->first_name . ' ' . optional($record->provider?->user)->last_name)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('price')
                    ->label(__('Price'))
                    ->money('EGP', true)
                    ->sortable(),

                Tables\Columns\IconColumn::make('has_delivery')
                    ->label(__('Delivery'))
                    ->boolean(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->formatStateUsing(fn ($state) => match((int) $state) {
                        0 => __('Pending'),
                        1 => __('Selected'),
                        2 => __('Rejected'),
                        default => __('Unknown'),
                    })
                    ->colors([
                        'warning' => 0,
                        'success' => 1,
                        'danger' => 2,
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
            ])
            ->bulkActions([
            ])
            ->striped()
            ->recordClasses(function ($record) {
                return (int) $record->status === 1 ? 'bg-success-50 dark:bg-success-900/30' : null;
            });
    }
}


