<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use App\Enums\PostStatus;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('accept')
                ->label(__('Accept'))
                ->color('success')
                ->visible(function () {
                    $value = is_int($this->record->status) ? $this->record->status : (int) $this->record->status;
                    if ($value === 0) { $value = PostStatus::Pending->value; }
                    return $value === PostStatus::Pending->value;
                })
                ->requiresConfirmation()
                ->action(function () {
                    app(PostService::class)->accept($this->record);
                    $this->refreshFormData(['status']);
                }),
            Actions\Action::make('reject')
                ->label(__('Reject'))
                ->color('danger')
                ->visible(function () {
                    $value = is_int($this->record->status) ? $this->record->status : (int) $this->record->status;
                    if ($value === 0) { $value = PostStatus::Pending->value; }
                    return $value === PostStatus::Pending->value;
                })
                ->form([
                    Forms\Components\Textarea::make('rejection_reason')->label(__('Rejection Reason'))->required(),
                ])
                ->requiresConfirmation()
                ->action(function (array $data) {
                    app(PostService::class)->reject($this->record, $data['rejection_reason'] ?? null);
                    $this->refreshFormData(['status', 'rejection_reason']);
                }),
        ];
    }
}


