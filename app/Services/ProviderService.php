<?php

namespace App\Services;

use App\Exceptions\BusinessLogicException;
use App\Models\Package;
use App\Models\Provider;
use App\Models\Subscription;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function App\Helpers\setting;
use function App\Helpers\settings;

class ProviderService extends BaseService
{
    protected $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
        parent::__construct($provider);
    }

    /**
     * Get Provider with relationships
     */
    public function getWithScopes(array $scopes = []): Collection
    {
        $query = $this->provider->query();
        if (!empty($scopes)) {
            $query->with($scopes);
        }
        return $query->get();
    }

    public function getCurrentPackage(Provider $provider): ?Subscription
    {
        $currentSubscription = $provider->subscriptions->where('is_active', true)->first();
        if($currentSubscription){
            return $currentSubscription->load('package');
        }
        return null;
    }

    public function getActiveProviders(): Collection
    {
        return $this->provider->whereHas('user', function($query){
            $query->where('is_active', true);
        })->get();
    }

    /**
     * Find Provider with relationships
     */
    public function findWithRelations(int $id, array $relations = []): ?Provider
    {
        return $this->provider->with($relations)->find($id);
    }

    /**
     * Get nearest providers to given coordinates using Haversine formula
     * @param float $lat Latitude of the point
     * @param float $long Longitude of the point
     * @param int $radius Maximum radius in kilometers (default: 50km)
     * @return Collection
     */
    public function getNearestProviders(float $lat, float $long, int $limit = null): Collection
{ 
    $query = $this->provider
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
        return $this->provider->limit($limit)->get();
    }
    }

    /**
     * Create Category with business logic
     */
    public function createWithBusinessLogic(array $data): Provider
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
    public function updateWithBusinessLogic(Provider $provider, array $data): bool
    {
        // Add your business logic here before updating
        $this->validateBusinessRules($data, $provider);
        
        $updated = $this->update($provider, $data);
        
        if ($updated) {
            // Add your business logic here after updating
            $this->afterUpdate($provider);
        }
        
        return $updated;
    }

    /**
     * Delete Category with business logic
     */
                public function deleteWithBusinessLogic(Provider $provider): bool
    {
        // Add your business logic here before deleting
        $this->validateDeletion($provider);
        
        $deleted = $this->delete($provider);
        
        if ($deleted) {
            // Add your business logic here after deleting
            $this->afterDelete($provider);
        }
        
        return $deleted;
    }

    /**
     * Get services for Category
     */
    public function getServices(Provider $provider): Collection
    {
        return $provider->services;
    }

    /**
     * Add services to Category
     */
    public function addService(Provider $provider, array $data): Model
    {
        return $provider->services()->create($data);
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
    public function stockCount(Provider $provider): int
    {
        return $provider->products->sum('stock');
    }

    public function updateDays(Provider $provider, array $days): bool
    {
        return $provider->days()->update($days);
    }
    
    public function SubscribeToPackage(Provider $provider, Package $package)
    {
        if($provider->subscriptions()->where('is_active', true)->exists()){
            throw new BusinessLogicException(__('Provider already has an active subscription'));
        }
        // if price has discount, calculate the price
        $finalPrice = $package->final_price;
        $subscription = $provider->subscriptions()->create([
            'package_id' => $package->id,
            'start_date' => now(),
            'end_date' => now()->addDays($package->duration),
            'is_active' => true,
            'total' => $finalPrice ? $finalPrice : $package->price,
        ]);
        return $subscription;
    }


    public function createProviderWithoutRequest(array $data): ?Provider{
        try{
        DB::transaction(function () use ($data) {
            // Create User
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'city_id' => $data['city_id'] == 0 ? null : $data['city_id'],
                'lat' => $data['lat'],
                'long' => $data['long'],
                'address' => $data['address'],
                'is_active' => true,
                'is_verified' => true,
            ]);

            // Assign provider role
            $role = Role::where('name', 'provider')->where('guard_name', 'sanctum')->first();
            // delete all roles from the user
            $user->roles()->delete();
            $user->assignRole($role);

            // Create Provider
            $provider = Provider::create([
                'user_id' => $user->id,
                'store_name' => [
                    'ar' => $data['store_name']['ar'],
                    'en' => $data['store_name']['en'],
                ],
                'description' => $data['description'],
                'commercial_number' => $data['commercial_number'],
                'location' => $data['location'],
                'category_id' => $data['category_id'],
                'city_id' => $data['city_id'] == 0 ? null : $data['city_id'],
            ]);

            // Ensure provider was created successfully with an ID
            if (!$provider || !$provider->id) {
                throw new \Exception(__('Failed to create provider record'));
            }

            // Handle brands relationship
                if ($data['brands']) {
                $brandIds = is_string($data['brands']) 
                    ? json_decode($data['brands'], true) 
                    : $data['brands'];
                
                if (is_array($brandIds) && !empty($brandIds)) {
                    // Filter out any invalid brand IDs
                    $validBrandIds = array_filter(array_map('intval', $brandIds), function($id) {
                        return $id > 0;
                    });
                    
                    if (!empty($validBrandIds)) {
                        $provider->brands()->sync($validBrandIds);
                    }
                }
            }

            // Copy media files from registration request to provider
            if(isset($data['logo'])){
                foreach ($data['logo'] as $media) {
                    $media->copy($provider, 'logo');
                }
            }   
            if(isset($data['commercial_number_image'])){
                foreach ($data['commercial_number_image'] as $media) {
                    $media->copy($provider, 'commercial_number_image');
                }   
            }

            // Update registration request status
            $provider->update(['status' => 1]);
        });
        return $this->provider;
        }catch(\Exception $e){
            Log::error('Failed to create provider without request: ' . $e->getMessage());
            return null;
        }
    }
}