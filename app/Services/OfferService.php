<?php

namespace App\Services;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OfferService extends BaseService
{
    protected $offer;

    public function __construct(Offer $offer,  private ProviderSearchService $providerSearchService)
                    {
        $this->offer = $offer;
        parent::__construct($offer);
    }

    /**
     * Get Category with relationships
     */
    public function getWithScopes(array $scopes = []): Collection
    {
        $query = $this->offer->query();
        foreach ($scopes as $scope) {
            $query->$scope();
        }
        return $query->get();
    }

    /**
     * Find Category with relationships
     */
    public function findWithRelations(int $id, array $relations = []): ?Offer
    {
        return $this->offer->with($relations)->find($id);
    }
    public function getModels(): Collection
    {
        return $this->offer->models;
    }

    /**
     * Create Category with business logic
     */
    public function createWithBusinessLogic(array $data): Offer
    {
        $data['user_id'] = Auth::id();
        $data['number'] = rand(100000, 999999);
        $data['status'] = 0;
 
        
                    $offer = $this->create($data);
        
        // Add your business logic here after creating
        $this->afterCreate($offer);
        
        return $offer;
    }

    /**
     * Update Category with business logic
     */
    public function updateWithBusinessLogic(Offer $offer, array $data): bool
    {
        // Add your business logic here before updating
        $this->validateBusinessRules($data, $offer);
        
        $updated = $this->update($offer, $data);
        
        if ($updated) {
            // Add your business logic here after updating
            $this->afterUpdate($offer);
        }
        
        return $updated;
    }

    /**
     * Delete Category with business logic
     */
    public function deleteWithBusinessLogic(Offer $offer): bool
    {
        // Add your business logic here before deleting
        $this->validateDeletion($offer);
        
        $deleted = $this->delete($offer);
        
        if ($deleted) {
            // Add your business logic here after deleting
            $this->afterDelete($offer);
        }
        
        return $deleted;
    }



    /**
     * Validate business rules
     */
    protected function validateBusinessRules(array $data, ?Offer $offer = null): void
    {
       
    }

    /**
     * Validate deletion
     */
            protected function validateDeletion(Offer $offer): void
    {
        // Add your deletion validation logic here
        // Example: Check if record can be deleted, has dependencies, etc.
    }

    /**
     * After create business logic
     */
    protected function afterCreate(Offer $offer): void
    {
        //TODO: Send Fcm Notifications to Providers
    }

    /**
     * After update business logic
     */
            protected function afterUpdate(Offer $offer): void
    {
        // Add your post-update business logic here
        // Example: Send notifications, update related records, etc.
    }

    /**
     * After delete business logic
     */
    protected function afterDelete(Offer $offer): void
    {
        // Add your post-deletion business logic here
        // Example: Clean up related records, send notifications, etc.
    }
    public function filterOffers(array $data){
        $user = Auth::user();
        $userLat = $user->lat ?? null;
        $userLong = $user->long ?? null;

        // Start building the query with relationships
        $query = $this->offer->with(['provider.user', 'provider.city']);

        // Filter by city_id
        if(!empty($data['city_id'])){
            $query->where('city_id', $data['city_id']);
        }

        // Apply default filters (only active providers)
        $query->whereHas('provider.user', function ($q) {
            $q->where('is_active', true);
        });

        // Handle ordering
        if($data['order_by'] == 1){
            // Order by nearest provider (user lat, long)
            if ($userLat && $userLong) {
                $query->select('offers.*')
                    ->selectRaw("
                        (6371 * acos(
                            cos(radians(?)) * 
                            cos(radians(users.lat)) * 
                            cos(radians(users.long) - radians(?)) + 
                            sin(radians(?)) * 
                            sin(radians(users.lat))
                        )) AS distance
                    ", [$userLat, $userLong, $userLat])
                    ->join('providers', 'offers.provider_id', '=', 'providers.id')
                    ->join('users', 'providers.user_id', '=', 'users.id')
                    ->whereNotNull('users.lat')
                    ->whereNotNull('users.long')
                    ->orderBy('distance', 'asc');
            } else {
                // If user doesn't have coordinates, filter by user's city
                $query->whereHas('provider', function($q) use ($user, $data) {
                    if(!empty($data['city_id'])){
                        $q->where('city_id', $data['city_id']);
                    } else {
                        $q->where('city_id', $user->city_id);
                    }
                });
            }
        } elseif($data['order_by'] == 2){
            // Order by provider rating (for future implementation)
            $query->orderBy('created_at', 'desc'); // Default ordering for now
            // TODO: Implement rating-based ordering when rating system is ready
            // $query->orderBy('provider.rating', 'desc');
        } else {
            // Default ordering by creation date
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    /**
     * Get filtered offers with pagination
     */
    public function getFilteredOffers(array $data, $perPage = 8)
    {
        $query = $this->filterOffers($data);
        return $query->paginate($perPage);
    }

    /**
     * Get filtered offers without pagination
     */
    public function getFilteredOffersList(array $data)
    {
        $query = $this->filterOffers($data);
        return $query->get();
    }
}