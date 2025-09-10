<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';


    public static function getModelLabel(): string
    {
        return __('Report');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Reports');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label(__('ID'))->sortable(),
                Tables\Columns\TextColumn::make('reporter.name')->label(__('Reporter'))->searchable(),
                Tables\Columns\TextColumn::make('reportable_type')->label(__('Type'))->formatStateUsing(function ($state) {
                    if (!$state) return '-';
                    return str_contains($state, 'Comment') ? __('Comment') : (str_contains($state, 'Provider') ? __('Provider') : $state);
                }),
                Tables\Columns\TextColumn::make('created_at')->label(__('Created At'))->dateTime()->since(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Report Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('id')->label(__('ID')),
                        Infolists\Components\TextEntry::make('reporter.name')->label(__('Reporter')),
                        Infolists\Components\TextEntry::make('reportable_type')->label(__('Type'))
                            ->formatStateUsing(function ($state) {
                                if (!$state) return '-';
                                return str_contains($state, 'Comment') ? __('Comment') : (str_contains($state, 'Provider') ? __('Provider') : $state);
                            }),
                        Infolists\Components\TextEntry::make('reportable_id')->label(__('Provider ID'))
                            ->visible(fn ($record) => $record->reportable && is_a($record->reportable, \App\Models\Provider::class)),
                        Infolists\Components\TextEntry::make('reportable_id')->label(__('Comment ID'))
                            ->visible(fn ($record) => $record->reportable && is_a($record->reportable, \App\Models\Comment::class)),
                        Infolists\Components\TextEntry::make('reason')->label(__('Reason'))->columnSpanFull(),
                        Infolists\Components\TextEntry::make('created_at')->label(__('Created At'))->dateTime('d/m/Y H:i'),
                    ])->columns(2),
                Infolists\Components\Section::make(__('Details'))
                    ->schema([
                        // If reportable is Comment: show comment content and its Post info
                        Infolists\Components\TextEntry::make('reportable.content')
                            ->label(__('Comment'))
                            ->visible(fn ($record) => $record->reportable && is_a($record->reportable, \App\Models\Comment::class))
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('reportable.post.id')
                            ->label(__('Post ID'))
                            ->visible(fn ($record) => $record->reportable && is_a($record->reportable, \App\Models\Comment::class)),
                        Infolists\Components\TextEntry::make('reportable.post.content')
                            ->label(__('Post Content'))
                            ->visible(fn ($record) => $record->reportable && is_a($record->reportable, \App\Models\Comment::class))
                            ->columnSpanFull(),

                        // If reportable is Provider: show user phone, email and reason
                        Infolists\Components\TextEntry::make('reportable.user.phone')
                            ->label(__('Phone'))
                            ->visible(fn ($record) => $record->reportable && is_a($record->reportable, \App\Models\Provider::class)),
                        Infolists\Components\TextEntry::make('reportable.user.email')
                            ->label(__('Email'))
                            ->visible(fn ($record) => $record->reportable && is_a($record->reportable, \App\Models\Provider::class)),
                        Infolists\Components\TextEntry::make('reason')
                            ->label(__('Reason'))
                            ->visible(fn ($record) => $record->reportable && is_a($record->reportable, \App\Models\Provider::class))
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'view' => Pages\ViewReport::route('/{record}'),
        ];
    }
}


