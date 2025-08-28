<?php

namespace App\Services;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProviderSearchService extends BaseSearchService
{
    public function __construct(private Provider $provider)
    {
        parent::__construct($this->provider, 'store_name', ['city', 'category', 'user'], ['is_active' => true]);
    }

    /**
     * Simple search for providers with filters and location sorting
     * 
     * @param array $filters - Contains query/q, category_id, city_id
     * @param int $perPage - Number of results per page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchProvidersWithLocation($filters = [], $perPage = 8)
    {
        $user = Auth::user();
        $userLat = $user->lat ?? null;
        $userLong = $user->long ?? null;

        // Start building the query
        $queryBuilder = $this->model->newQuery()
            ->with(['city', 'category', 'user']);

        // Apply search if query is provided
        $searchQuery = $filters['q'] ?? $filters['query'] ?? null;
        if (!empty($searchQuery)) {
            $searchQuery = trim($searchQuery);
            if (strlen($searchQuery) >= 3) {
                // Simple search in JSON field - check both languages
                $queryBuilder->where(function($q) use ($searchQuery) {
                    $q->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(store_name, "$.ar")) LIKE ?', ['%' . $searchQuery . '%'])
                      ->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(store_name, "$.en")) LIKE ?', ['%' . $searchQuery . '%']);
                });
            }
        }

        // Apply category filter
        if (!empty($filters['category_id'])) {
            $queryBuilder->where('providers.category_id', $filters['category_id']);
        }

        // Apply city filter
        if (!empty($filters['city_id'])) {
            $queryBuilder->where('providers.city_id', $filters['city_id']);
        }

        // Apply default filters (is_active = true through user relationship)
        $queryBuilder->whereHas('user', function ($q) {
            $q->where('is_active', true);
        });

        // Add distance calculation and sorting if user has coordinates
        if ($userLat && $userLong) {
            $queryBuilder->select('providers.*')
                ->selectRaw("
                    (6371 * acos(
                        cos(radians(?)) * 
                        cos(radians(users.lat)) * 
                        cos(radians(users.long) - radians(?)) + 
                        sin(radians(?)) * 
                        sin(radians(users.lat))
                    )) AS distance
                ", [$userLat, $userLong, $userLat])
                ->join('users', 'providers.user_id', '=', 'users.id')
                ->whereNotNull('users.lat')
                ->whereNotNull('users.long')
                ->orderBy('distance', 'asc');
        } else {
            // If no user coordinates, order by store name
            $queryBuilder->orderBy('store_name');
        }

        return $queryBuilder->paginate($perPage);
    }

    /**
     * Override the base applyFilters method to handle provider-specific filters
     */
    protected function applyFilters(Builder $queryBuilder, $request): Builder
    {
        // Apply category filter
        if ($request->has('category_id') && !empty($request->query('category_id'))) {
            $queryBuilder->where('providers.category_id', $request->query('category_id'));
        }

        // Apply city filter
        if ($request->has('city_id') && !empty($request->query('city_id'))) {
            $queryBuilder->where('providers.city_id', $request->query('city_id'));
        }

        // Apply default filters through user relationship
        $queryBuilder->whereHas('user', function ($q) {
            $q->where('is_active', true);
        });

        return $queryBuilder;
    }
}
