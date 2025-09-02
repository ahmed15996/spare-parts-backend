<?php

namespace App\Filament\Resources\BannerResource\Pages;

use App\Filament\Resources\BannerResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use App\Enums\BannerStatus;
use App\Models\Banner;
use Filament\Notifications\Notification;

class ViewBanner extends ViewRecord
{
    protected static string $resource = BannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label(__('Approve'))
                ->requiresConfirmation()
                ->visible(fn() => $this->record->status === BannerStatus::Pending)
                ->action(function () {
                    $this->record->status = BannerStatus::Approved;
                    $this->record->accepted_at = now();
                    $this->record->rejection_reason = null;
                    $this->record->save();
                  
                    Notification::make()
                        ->title(__('Banner approved'))
                        ->success()
                        ->send();
                    $this->record->refresh();
                }),
            Actions\Action::make('reject')
                ->label(__('Reject'))
                ->form([
                    \Filament\Forms\Components\Textarea::make('rejection_reason')->label(__('Rejection Reason'))->required(),
                ])
                ->visible(fn() => $this->record->status === BannerStatus::Pending)
                ->action(function (array $data) {
                    $this->record->status = BannerStatus::Rejected;
                    $this->record->rejection_reason = $data['rejection_reason'] ?? null;
                    $this->record->save();
                    Notification::make()
                        ->title(__('Banner rejected'))
                        ->success()
                        ->send();
                    $this->record->refresh();
                }),
        ];
    }

    public function getTitle(): string
    {
        return __('View Banner');
    }
}


