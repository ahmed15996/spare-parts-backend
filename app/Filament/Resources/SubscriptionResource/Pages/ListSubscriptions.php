<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('All Subscriptions'))
                ->badge(fn () => \App\Models\Subscription::count()),

            'active' => Tab::make(__('Active'))
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('is_active', true)
                          ->where('end_date', '>=', Carbon::today())
                )
                ->badge(fn () => \App\Models\Subscription::where('is_active', true)
                    ->where('end_date', '>=', Carbon::today())
                    ->count())
                ->badgeColor('success'),

            'expiring_soon' => Tab::make(__('Expiring Soon'))
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('is_active', true)
                          ->whereBetween('end_date', [
                              Carbon::today(),
                              Carbon::today()->addDays(7)
                          ])
                )
                ->badge(fn () => \App\Models\Subscription::where('is_active', true)
                    ->whereBetween('end_date', [
                        Carbon::today(),
                        Carbon::today()->addDays(7)
                    ])
                    ->count())
                ->badgeColor('warning'),

            'expired' => Tab::make(__('Expired'))
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('end_date', '<', Carbon::today())
                )
                ->badge(fn () => \App\Models\Subscription::where('end_date', '<', Carbon::today())
                    ->count())
                ->badgeColor('danger'),

            'inactive' => Tab::make(__('Inactive'))
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('is_active', false)
                )
                ->badge(fn () => \App\Models\Subscription::where('is_active', false)
                    ->count())
                ->badgeColor('gray'),
        ];
    }
}

