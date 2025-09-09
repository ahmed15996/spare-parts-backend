<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Provider;
use App\Models\Request;
use App\Models\Offer;
use App\Models\Category;
use App\Models\ProviderRegistrationRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdvancedDashboardStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        return [
            // Core Business Metrics
            $this->getTotalProvidersStat(),
            $this->getTotalClientsStat(),
            $this->getTotalRequestsStat(),
            $this->getTotalOffersStat(),
            
            // Registration & Growth Metrics
            $this->getRegistrationRequestsStat(),
            $this->getRegistrationRateStat(),
            $this->getMonthlyGrowthStat(),
            $this->getActiveUsersStat(),
            
            // Request & Offer Performance
            $this->getPendingRequestsStat(),
            
            // Category & Service Insights
            $this->getTotalCategoriesStat(),
            $this->getAverageOffersPerRequestStat(),
        ];
    }

    private function getTotalProvidersStat(): Stat
    {
        $total = Provider::count();
        $active = Provider::whereHas('user', function ($query) {
            $query->where('is_active', true);
        })->count();
        
        return Stat::make(__('stats.total_providers'), $total)
            ->description(__('stats.active_providers_count', ['count' => $active]))
            ->descriptionIcon('heroicon-m-building-storefront')
            ->color('success')
            ->chart($this->getProviderGrowthChart());
    }

    private function getTotalClientsStat(): Stat
    {
        $total = User::whereHas('roles', function ($query) {
            $query->where('name', 'client');
        })->count();
        
        $active = User::whereHas('roles', function ($query) {
            $query->where('name', 'client');
        })->where('is_active', true)->count();
        
        return Stat::make(__('stats.total_clients'), $total)
            ->description(__('stats.active_clients_count', ['count' => $active]))
            ->descriptionIcon('heroicon-m-users')
            ->color('primary')
            ->chart($this->getClientGrowthChart());
    }

    private function getTotalRequestsStat(): Stat
    {
        $total = Request::count();
        $thisMonth = Request::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        return Stat::make(__('stats.total_requests'), $total)
            ->description(__('stats.requests_this_month', ['count' => $thisMonth]))
            ->descriptionIcon('heroicon-m-clipboard-document-list')
            ->color('info')
            ->chart($this->getRequestTrendChart());
    }

    private function getTotalOffersStat(): Stat
    {
        $total = Offer::count();
        $thisMonth = Offer::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        return Stat::make(__('stats.total_offers'), $total)
            ->description(__('stats.offers_this_month', ['count' => $thisMonth]))
            ->descriptionIcon('heroicon-m-currency-dollar')
            ->color('success')
            ->chart($this->getOfferTrendChart());
    }

    private function getRegistrationRequestsStat(): Stat
    {
        $pending = ProviderRegistrationRequest::where('status', 'pending')->count();
        $total = ProviderRegistrationRequest::count();
        
        return Stat::make(__('stats.registration_requests'), $total)
            ->description(__('stats.pending_approval_count', ['count' => $pending]))
            ->descriptionIcon('heroicon-m-user-plus')
            ->color('warning');
    }

    private function getRegistrationRateStat(): Stat
    {
        $totalRequests = ProviderRegistrationRequest::count();
        $approvedRequests = ProviderRegistrationRequest::where('status', 1)->count();
        
        $rate = $totalRequests > 0 ? ($approvedRequests / $totalRequests) * 100 : 0;
        
        return Stat::make(__('stats.approval_rate'), number_format($rate, 1) . '%')
            ->description(__('stats.provider_registration_success_rate'))
            ->descriptionIcon('heroicon-m-check-badge')
            ->color($rate >= 70 ? 'success' : ($rate >= 50 ? 'warning' : 'danger'));
    }

    private function getMonthlyGrowthStat(): Stat
    {
        $currentMonth = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['client', 'provider']);
        })->whereMonth('created_at', now()->month)
          ->whereYear('created_at', now()->year)
          ->count();

        $lastMonth = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['client', 'provider']);
        })->whereMonth('created_at', now()->subMonth()->month)
          ->whereYear('created_at', now()->subMonth()->year)
          ->count();

        $growth = $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;
        $sign = $growth >= 0 ? '+' : '';
        
        return Stat::make(__('stats.monthly_growth'), $sign . number_format($growth, 1) . '%')
            ->description(__('stats.new_users_this_month', ['count' => $currentMonth]))
            ->descriptionIcon($growth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($growth >= 0 ? 'success' : 'danger');
    }

    private function getActiveUsersStat(): Stat
    {
        $activeClients = User::whereHas('roles', function ($query) {
            $query->where('name', 'client');
        })->where('is_active', true)->count();
        
        $activeProviders = Provider::whereHas('user', function ($query) {
            $query->where('is_active', true);
        })->count();
        
        $total = $activeClients + $activeProviders;
        
        return Stat::make(__('stats.active_users'), $total)
            ->description(__('stats.active_clients_providers', ['clients' => $activeClients, 'providers' => $activeProviders]))
            ->descriptionIcon('heroicon-m-user-group')
            ->color('primary');
    }

    private function getPendingRequestsStat(): Stat
    {
        $pending = Request::whereDoesntHave('offers')->count();
        $total = Request::count();
        $percentage = $total > 0 ? ($pending / $total) * 100 : 0;
        
        return Stat::make(__('stats.pending_requests'), $pending)
            ->description(__('stats.percent_of_total_requests', ['percent' => number_format($percentage, 1)]))
            ->descriptionIcon('heroicon-m-clock')
            ->color('warning');
    }





    private function getTotalCategoriesStat(): Stat
    {
        $total = Category::count();
        // Count categories linked to at least one request via requests table
        $withRequests = Category::whereHas('requests')->count();
        
        return Stat::make(__('stats.service_categories'), $total)
            ->description(__('stats.categories_with_active_requests', ['count' => $withRequests]))
            ->descriptionIcon('heroicon-m-squares-2x2')
            ->color('gray');
    }

    private function getMostPopularCategoryStat(): Stat
    {
        $category = Request::select('categories.name')
            ->join('categories', 'requests.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, COUNT(*) as request_count')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('request_count')
            ->first();

        $name = $category ? $category->name : 'N/A';
        $count = $category ? $category->request_count : 0;
        
        return Stat::make(__('stats.most_popular_category'), $name)
            ->description(__('stats.requests_count', ['count' => $count]))
            ->descriptionIcon('heroicon-m-star')
            ->color('warning');
    }

    private function getAverageOffersPerRequestStat(): Stat
    {
        $avgOffers = Request::withCount('offers')->get()->avg('offers_count');
        $avgFormatted = $avgOffers ? number_format($avgOffers, 1) : '0';
        
        return Stat::make(__('stats.avg_offers_per_request'), $avgFormatted)
            ->description(__('stats.competition_level'))
            ->descriptionIcon('heroicon-m-chart-bar')
            ->color('info');
    }

    private function getConversionRateStat(): Stat
    {
        $totalRequests = Request::count();
        $requestsWithOffers = Request::whereHas('offers')->count();
        $rate = $totalRequests > 0 ? ($requestsWithOffers / $totalRequests) * 100 : 0;
        
        return Stat::make(__('stats.conversion_rate'), number_format($rate, 1) . '%')
            ->description(__('stats.requests_receiving_offers'))
            ->descriptionIcon('heroicon-m-arrow-path')
            ->color($rate >= 80 ? 'success' : ($rate >= 60 ? 'warning' : 'danger'));
    }

    // Chart data methods
    private function getProviderGrowthChart(): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Provider::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getClientGrowthChart(): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = User::whereHas('roles', function ($query) {
                $query->where('name', 'client');
            })->whereMonth('created_at', $date->month)
              ->whereYear('created_at', $date->year)
              ->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getRequestTrendChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Request::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getOfferTrendChart(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Offer::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }
}
