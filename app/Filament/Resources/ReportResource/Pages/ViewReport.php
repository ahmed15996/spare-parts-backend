<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use App\Models\Comment;
use App\Models\Provider;
use App\Models\User;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewReport extends ViewRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('delete_comment')
                ->label(__('Delete Comment'))
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->visible(fn () => $this->getRecord()->reportable instanceof Comment)
                ->action(function () {
                    $record = $this->getRecord();
                    if ($record->reportable instanceof Comment) {
                        $record->reportable->delete();
                    }
                }),
            Actions\Action::make('ban_user')
                ->label(__('Ban User'))
                ->icon('heroicon-o-user-minus')
                ->requiresConfirmation()
                ->visible(function () {
                    $record = $this->getRecord();
                    if ($record->reportable instanceof Comment) {
                        $author = $record->reportable->author;
                        return $author instanceof User && (bool) $author->is_active;
                    }
                    return false;
                })
                ->action(function () {
                    $record = $this->getRecord();
                    if ($record->reportable instanceof Comment) {
                        $author = $record->reportable->author;
                        if ($author instanceof User) {
                            $author->is_active = false;
                            $author->save();
                        }
                    }
                }),
            Actions\Action::make('ban_provider')
                ->label(__('Ban Provider'))
                ->icon('heroicon-o-building-office-2')
                ->requiresConfirmation()
                ->visible(function () {
                    $record = $this->getRecord();
                    if ($record->reportable instanceof Provider) {
                        $user = $record->reportable->user;
                        return $user instanceof User && (bool) $user->is_active;
                    }
                    return false;
                })
                ->action(function () {
                    $record = $this->getRecord();
                    if ($record->reportable instanceof Provider) {
                        $user = $record->reportable->user;
                        if ($user instanceof User) {
                            $user->is_active = false;
                            $user->save();
                        }
                    }
                }),
        ];
    }
}


