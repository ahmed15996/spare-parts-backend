<?php

namespace App\Filament\Resources;

use App\Enums\DeleteAccountRequestStatus;
use App\Filament\Resources\DeleteAccountRequestResource\Pages;
use App\Models\DeleteAccountRequest;
use App\Models\DeleteAccountReason;
use App\Models\Provider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class DeleteAccountRequestResource extends Resource
{
    protected static ?string $model = DeleteAccountRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-trash';

    public static function getNavigationLabel(): string
    {
        return __('Delete Account Requests');
    }

    public static function getModelLabel(): string
    {
        return __('Delete Account Request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Delete Account Requests');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Provider Requests');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('provider_id')
                    ->label(__('Provider'))
                    ->relationship('provider', 'store_name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->native(false),

                Forms\Components\Select::make('reason_id')
                    ->label(__('Reason'))
                    ->relationship('reason', 'reason')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->native(false),

                Forms\Components\Select::make('status')
                    ->label(__('Status'))
                    ->options([
                        DeleteAccountRequestStatus::Pending->value => DeleteAccountRequestStatus::Pending->label(),
                        DeleteAccountRequestStatus::Approved->value => DeleteAccountRequestStatus::Approved->label(),
                        DeleteAccountRequestStatus::Rejected->value => DeleteAccountRequestStatus::Rejected->label(),
                    ])
                    ->required()
                    ->native(false)
                    ->default(DeleteAccountRequestStatus::Pending->value),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('provider.store_name')
                    ->label(__('Provider'))
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('reason.reason')
                    ->label(__('Reason'))
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->formatStateUsing(fn (DeleteAccountRequestStatus $state): string => $state->label())
                    ->badge()
                    ->color(fn (DeleteAccountRequestStatus $state): string => match ($state) {
                        DeleteAccountRequestStatus::Pending => 'warning',
                        DeleteAccountRequestStatus::Approved => 'success',
                        DeleteAccountRequestStatus::Rejected => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Requested At'))
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        DeleteAccountRequestStatus::Pending->value => DeleteAccountRequestStatus::Pending->label(),
                        DeleteAccountRequestStatus::Approved->value => DeleteAccountRequestStatus::Approved->label(),
                        DeleteAccountRequestStatus::Rejected->value => DeleteAccountRequestStatus::Rejected->label(),
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('reason_id')
                    ->label(__('Reason'))
                    ->relationship('reason', 'reason')
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Action::make('approve')
                    ->label(__('Approve'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (DeleteAccountRequest $record): bool => $record->status === DeleteAccountRequestStatus::Pending)
                    ->requiresConfirmation()
                    ->modalHeading(__('Approve Delete Account Request'))
                    ->modalDescription(__('Are you sure you want to approve this delete account request? This action will mark the request as approved.'))
                    ->action(function (DeleteAccountRequest $record) {
                        $record->update(['status' => DeleteAccountRequestStatus::Approved]);

                        try {
                            $provider = $record->provider->user;
                            
                            // Delete related records first to avoid foreign key constraints
                            $provider->fcmTokens()->delete();
                            $provider->customNotifications()->delete();
                            
                            // Delete the provider record first
                            $record->provider->delete();
                            
                            // Finally delete the user
                            $provider->delete();

                            Notification::make()
                                ->title(__('Request Approved'))
                                ->body(__('Delete account request has been approved successfully.'))
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title(__('Error'))
                                ->body(__('Failed to delete account. Please try again or contact support.'))
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('reject')
                    ->label(__('Reject'))
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (DeleteAccountRequest $record): bool => $record->status === DeleteAccountRequestStatus::Pending)
                    ->requiresConfirmation()
                    ->modalHeading(__('Reject Delete Account Request'))
                    ->modalDescription(__('Are you sure you want to reject this delete account request? This action will mark the request as rejected.'))
                    ->action(function (DeleteAccountRequest $record) {
                        $record->update(['status' => DeleteAccountRequestStatus::Rejected]);
                        
                        Notification::make()
                            ->title(__('Request Rejected'))
                            ->body(__('Delete account request has been rejected successfully.'))
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeleteAccountRequests::route('/'),
            'create' => Pages\CreateDeleteAccountRequest::route('/create'),
            'view' => Pages\ViewDeleteAccountRequest::route('/{record}'),
            'edit' => Pages\EditDeleteAccountRequest::route('/{record}/edit'),
        ];
    }
}
