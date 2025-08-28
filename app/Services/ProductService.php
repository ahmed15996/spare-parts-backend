<?php

namespace App\Services;

use App\Models\Package;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ProductService extends BaseService
{
    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
        parent::__construct($product);
    }

    /**
     * Get Provider with relationships
     */
    public function getWithScopes(array $scopes = []): Collection
    {
        $query = $this->product->query();
        if (!empty($scopes)) {
            $query->with($scopes);
        }
        return $query->get();
    }



    /**
     * Find Category with relationships
     */
    public function findWithRelations(int $id, array $relations = []): ?Product
    {
        
        return $this->product->where('id',$id)->with($relations)->first();
    }

    /**
     * Get nearest providers to given coordinates using Haversine formula
     * @param float $lat Latitude of the point
     * @param float $long Longitude of the point
     * @param int $radius Maximum radius in kilometers (default: 50km)
     * @return Collection
     */
    public function getNearestProducts(float $lat, float $long, int $limit = null): Collection
{ 
    $query = $this->product
        ->join('users', 'providers.user_id', '=', 'users.id')
        ->whereNotNull('users.lat')
        ->whereNotNull('users.long')
        ->selectRaw("
            providers.*,
            users.lat,
            users.long,
            (6371 * acos(
                cos(radians(?)) * 
                cos(radians(users.lat)) * 
                cos(radians(users.long) - radians(?)) + 
                sin(radians(?)) * 
                sin(radians(users.lat))
            )) AS distance
        ", [$lat, $long, $lat])
        ->orderBy('distance', 'asc');

    if ($limit) {
        $query->limit($limit);
    }

    $providers =  $query->get();
    if($providers->count() > 0){
        return $providers;
    }else{
        //return all providers
        return $this->product->limit($limit)->get();
    }
    }

    /**
     * Create Category with business logic
     */
    public function createWithBusinessLogic(array $data): Product
    {
        // Add your business logic here before creating
        $this->validateBusinessRules($data);
        
        $provider = $this->create($data);
        
        // Add your business logic here after creating
        $this->afterCreate($provider);
        
        return $provider;
    }

    /**
     * Update Category with business logic
     */
    // public function updateWithBusinessLogic(Product $product, array $data): bool
    // {
    //     // Add your business logic here before updating
    //     $this->validateBusinessRules($data, $provider);
        
    //     $updated = $this->update($provider, $data);
        
    //     if ($updated) {
    //         // Add your business logic here after updating
    //         $this->afterUpdate($provider);
    //     }
        
    //     return $updated;
    // }

    /**
     * Delete Category with business logic
     */
    // public function deleteWithBusinessLogic(Product $product): bool
    // {
    //     // Add your business logic here before deleting
    //     $this->validateDeletion($product);
        
    //     $deleted = $this->delete($product);
        
    //     if ($deleted) {
    //         // Add your business logic here after deleting
    //         $this->afterDelete($product);
    //     }
        
    //     return $deleted;
    // }

    /**
     * Get services for Category
     */
    public function getServices(Product $product): Collection
    {
        return $product->services;
    }

    /**
     * Add services to Category
     */
    public function addService(Product $product, array $data): Model
    {
        return $product->services()->create($data);
    }

    /**
     * Validate business rules
     */
    protected function validateBusinessRules(array $data, ?Provider $provider = null): void
    {
        // Add your business validation logic here
        // Example: Check if required fields are present, validate relationships, etc.
    }

    /**
     * Validate deletion
     */
    protected function validateDeletion(Provider $provider): void
    {
        // Add your deletion validation logic here
        // Example: Check if record can be deleted, has dependencies, etc.
    }

    /**
     * After create business logic
     */
    protected function afterCreate(Provider $provider): void
    {
        // Add your post-creation business logic here
        // Example: Send notifications, update related records, etc.
    }

    /**
     * After update business logic
     */
    protected function afterUpdate(Provider $provider): void
    {
        // Add your post-update business logic here
        // Example: Send notifications, update related records, etc.
    }

    /**
     * After delete business logic
     */
    protected function afterDelete(Provider $provider): void
    {
        // Add your post-deletion business logic here
        // Example: Clean up related records, send notifications, etc.
    }
}