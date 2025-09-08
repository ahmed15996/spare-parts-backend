<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Provider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductSearchService extends BaseSearchService
{
    public function __construct(private Product $product)
    {
        parent::__construct($this->product, 'name', ['commission', 'provider','media']);
    }

    /**
     * Simple search for products with filters and location sorting
     * 
     * @param array $filters - Contains query/q, category_id, city_id
     * @param int $perPage - Number of results per page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search($filters = [], $perPage = 8)
    {
    
        // Start building the query
        $queryBuilder = $this->model->newQuery()
            ->with([ 'media', 'provider']);

        // Apply search if query is provided
        $searchQuery = $filters['q'] ?? $filters['query'] ?? null;
        if (!empty($searchQuery)) {
            $searchQuery = trim($searchQuery);
            if (strlen($searchQuery) >= 3) {
                // Simple search in JSON field - check both languages
                $queryBuilder->where('name','like','%' . $searchQuery . '%');
            }
        }

        return $queryBuilder->paginate($perPage);
    }

}
