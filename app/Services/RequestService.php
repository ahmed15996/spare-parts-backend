<?php

namespace App\Services;

use App\Models\Provider;
use App\Models\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request as HttpRequest;

class RequestService extends BaseService
{
    protected $request;

    public function __construct(Request $request)
                    {
        $this->request = $request;
        parent::__construct($request);
    }

    /**
     * Get Category with relationships
     */
    public function getWithScopes(array $scopes = []): Collection
    {
        $query = $this->request->query();
        foreach ($scopes as $scope) {
            $query->$scope();
        }
        return $query->get();
    }

    /**
     * Find Category with relationships
     */
    public function findWithRelations(int $id, array $relations = []): ?Request
    {
        return $this->request->with($relations)->find($id);
    }
    public function getModels(): Collection
    {
        return $this->request->models;
    }

    /**
     * Create Category with business logic
     */
    public function createWithBusinessLogic(array $data): Request
    {
        $data['user_id'] = Auth::id();
        $data['number'] = rand(100000, 999999);
        $data['status'] = 0;
 
        
        $request = $this->create($data);
        
        // Add your business logic here after creating
        $this->afterCreate($request);
        
        return $request;
    }

    /**
     * Update Category with business logic
     */
    public function updateWithBusinessLogic(Request $request, array $data): bool
    {
        // Add your business logic here before updating
        $this->validateBusinessRules($data, $request);
        
        $updated = $this->update($request, $data);
        
        if ($updated) {
            // Add your business logic here after updating
            $this->afterUpdate($request);
        }
        
        return $updated;
    }

    /**
     * Delete Category with business logic
     */
    public function deleteWithBusinessLogic(Request $request): bool
    {
        // Add your business logic here before deleting
        $this->validateDeletion($request);
        
        $deleted = $this->delete($request);
        
        if ($deleted) {
            // Add your business logic here after deleting
            $this->afterDelete($request);
        }
        
        return $deleted;
    }



    /**
     * Validate business rules
     */
    protected function validateBusinessRules(array $data, ?Request $request = null): void
    {
       
    }

    /**
     * Validate deletion
     */
    protected function validateDeletion(Request $request): void
    {
        // Add your deletion validation logic here
        // Example: Check if record can be deleted, has dependencies, etc.
    }

    /**
     * After create business logic
     */
    protected function afterCreate(Request $request): void
    {
        //TODO: Send Fcm Notifications to Providers
    }

    /**
     * After update business logic
     */
            protected function afterUpdate(Request $request): void
    {
        // Add your post-update business logic here
        // Example: Send notifications, update related records, etc.
    }

    /**
     * After delete business logic
     */
    protected function afterDelete(Request $request): void
    {
        // Add your post-deletion business logic here
        // Example: Clean up related records, send notifications, etc.
    }

    public function getProviderRequests(Provider $provider , HttpRequest $request){
        $perPage = $request->query('per_page', 10);
        $day = $request->query('day', 'today');

        $query = $this->request;

        $query->where('category_id', $provider->category_id);
        $query->where('city_id', $provider->city_id);
        $query->whereHas('car', function($q) use ($provider){
            $q->whereHas('brand',function($q) use ($provider){
                $q->whereIn('id', $provider->brands->pluck('id'));
            });
            
        });

        if($day == 'today'){
            $query->whereDate('created_at', today());
        }

        return $query->paginate($perPage);

    }
}