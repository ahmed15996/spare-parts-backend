<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferResource\Pages;
use App\Models\Offer;
use Filament\Resources\Resource;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Don't show in navigation since it's only for viewing
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getLabel(): ?string
    {
        return __('Offer');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Offers');
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Offer Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label(__('ID')),
                        
                        Infolists\Components\TextEntry::make('price')
                            ->label(__('Price'))
                            ->money('EGP', true),
                        
                        Infolists\Components\TextEntry::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull(),
                        
                        Infolists\Components\IconEntry::make('has_delivery')
                            ->label(__('Has Delivery'))
                            ->boolean(),
                       
                    ])
                    ->columns(2),

               

             

                Infolists\Components\Section::make(__('Location Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('city.name')
                            ->label(__('Offer City'))
                            ->placeholder(__('Not specified')),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('Timestamps'))
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label(__('Created At'))
                            ->dateTime(),
                        
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label(__('Updated At'))
                            ->dateTime(),
                    ])
                    ->columns(2),
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
            'view' => Pages\ViewOffer::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['provider.user', 'request.user', 'request.category', 'city']);
    }
}