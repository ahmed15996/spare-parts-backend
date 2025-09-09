<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\EditAction::make(),
        ];

        // Show block action if user is active
        if ($this->record->is_active) {
            $actions[] = Actions\Action::make('block')
                ->label(__('Block User'))
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('Block User'))
                ->modalDescription(__('Are you sure you want to block this user? This will deactivate their account.'))
                ->action(function () {
                    try {
                        $this->record->is_active = false;
                        $this->record->save();
                        
                        Notification::make()
                            ->title(__('User blocked successfully'))
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title(__('Failed to block user'))
                            ->body(__('An error occurred while blocking the user.'))
                            ->danger()
                            ->send();
                    }
                });
        }

        // Show unblock action if user is blocked
        if (!$this->record->is_active) {
            $actions[] = Actions\Action::make('unblock')
                ->label(__('Unblock User'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(__('Unblock User'))
                ->modalDescription(__('Are you sure you want to unblock this user? This will reactivate their account.'))
                ->action(function () {
                    try {
                        $this->record->is_active = true;
                        $this->record->save();
                        
                        Notification::make()
                            ->title(__('User unblocked successfully'))
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title(__('Failed to unblock user'))
                            ->body(__('An error occurred while unblocking the user.'))
                            ->danger()
                            ->send();
                    }
                });
        }

        return $actions;
    }

    public function getTitle(): string
    {
        return __('View Client');
    }
}
