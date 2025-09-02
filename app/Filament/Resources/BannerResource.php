<?php

namespace App\Filament\Resources;

use App\Enums\BannerStatus;
use App\Enums\BannerType;
use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    public static function getNavigationGroup(): ?string
    {
        return __('Provider Requests');
    }

    public static function getModelLabel(): string
    {
        return __('Banner');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Banners');
    }

    public static function form(Form $form): Form
    {
        // No create/update; admins only view and moderate banners
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')->label(__('Number'))->sortable(),
                Tables\Columns\TextColumn::make('title')->label(__('Title'))->searchable(),
                Tables\Columns\TextColumn::make('provider.store_name')->label(__('Provider')),
                Tables\Columns\BadgeColumn::make('status')->label(__('Status'))
                    ->formatStateUsing(function ($state) {
                        $enum = $state instanceof BannerStatus ? $state : BannerStatus::from((int) $state);
                        return $enum->label();
                    })
                    ->colors([
                        'warning' => function ($state) {
                            $enum = $state instanceof BannerStatus ? $state : BannerStatus::from((int) $state);
                            return $enum === BannerStatus::Pending;
                        },
                        'success' => function ($state) {
                            $enum = $state instanceof BannerStatus ? $state : BannerStatus::from((int) $state);
                            return $enum === BannerStatus::Approved;
                        },
                        'danger' => function ($state) {
                            $enum = $state instanceof BannerStatus ? $state : BannerStatus::from((int) $state);
                            return $enum === BannerStatus::Rejected;
                        },
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'view' => Pages\ViewBanner::route('/{record}'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Provider Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('provider.store_name')->label(__('Provider Store')),
                        Infolists\Components\TextEntry::make('provider.user.phone')->label(__('Provider Phone')),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make(__('Banner Details'))
                    ->schema([
                        Infolists\Components\TextEntry::make('number')->label(__('Number')),
                        Infolists\Components\TextEntry::make('created_at')->label(__('Requested At'))->dateTime()->date('d/m/y'),
                        Infolists\Components\TextEntry::make('title')->label(__('Title')),
                        Infolists\Components\TextEntry::make('description')->label(__('Description'))->columnSpanFull(),
                        Infolists\Components\TextEntry::make('type')
                            ->label(__('Type'))
                            ->formatStateUsing(function ($state) {
                                $labels = [1 => __('Main Section'), 2 => __('Profile Section'), 3 => __('Main and Profile Section')];
                                return $labels[(int) $state] ?? (string) $state;
                            }),
                        Infolists\Components\TextEntry::make('original_price')
                            ->label(__('Original Price'))
                            ->visible(fn($record) => !is_null($record->original_price)),
                        Infolists\Components\TextEntry::make('discount_percentage')
                            ->label(__('Discount %'))
                            ->suffix('%')
                            ->visible(fn($record) => !is_null($record->discount_percentage)),
                        Infolists\Components\TextEntry::make('discount_price')
                            ->label(__('Discount Price'))
                            ->visible(fn($record) => !is_null($record->discount_price)),
                        Infolists\Components\TextEntry::make('status')
                            ->label(__('Status'))
                            ->badge()
                            ->formatStateUsing(function ($state) {
                                $enum = $state instanceof \App\Enums\BannerStatus ? $state : \App\Enums\BannerStatus::from((int) $state);
                                return $enum->label();
                            })
                            ->color(function ($state) {
                                $enum = $state instanceof \App\Enums\BannerStatus ? $state : \App\Enums\BannerStatus::from((int) $state);
                                return match ($enum) {
                                    \App\Enums\BannerStatus::Pending => 'warning',
                                    \App\Enums\BannerStatus::Approved => 'success',
                                    \App\Enums\BannerStatus::Rejected => 'danger',
                                };
                            }),
                        Infolists\Components\TextEntry::make('accepted_at')->label(__('Accepted At'))->dateTime()->visible(function ($record) {
                            $status = $record->status instanceof BannerStatus ? $record->status : BannerStatus::from((int) $record->status);
                            return $status === BannerStatus::Approved;
                        })
                        ->date('d/m/y')
                        ,
                        Infolists\Components\TextEntry::make('rejection_reason')->label(__('Rejection Reason'))->visible(function ($record) {
                            $status = $record->status instanceof BannerStatus ? $record->status : BannerStatus::from((int) $record->status);
                            return $status === BannerStatus::Rejected;
                        }),
                        Infolists\Components\ImageEntry::make('image')
                            ->label(__('Image'))
                            ->getStateUsing(fn($record) => $record->getFirstMediaUrl('image'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}


