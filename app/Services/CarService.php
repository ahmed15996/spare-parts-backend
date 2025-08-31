<?php

namespace App\Services;

use App\Models\Car;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CarService extends BaseService
{
    protected $car;

    public function __construct(Car $car)
    {
        $this->car = $car;
        parent::__construct($car);
    }

    /**
     * Get Category with relationships
     */
    public function getWithScopes(array $scopes = []): Collection
    {
        $query = $this->car->query();
        foreach ($scopes as $scope) {
            $query->$scope();
        }
        return $query->get();
    }

    /**
     * Find Category with relationships
     */
    public function findWithRelations(int $id, array $relations = []): ?Car
    {
        return $this->car->with($relations)->find($id);
    }

    /**
     * Create Category with business logic
     */
    public function createWithBusinessLogic(array $data): Car
    {
        // Add your business logic here before creating
        $this->validateBusinessRules($data);
        
        $car = $this->create($data);
        
        // Add your business logic here after creating
        // $this->afterCreate($car);
        
        return $car;
    }

    /**
     * Update Category with business logic
     */
    public function updateWithBusinessLogic(Car $car, array $data): bool
    {
        // Add your business logic here before updating
        $this->validateBusinessRules($data, $car);
        
        // Debug: Log the data being updated
        \Illuminate\Support\Facades\Log::info('CarService: Updating car with data:', $data);
        
        $updated = $this->update($car, $data);
        
        // Debug: Log the result
        \Illuminate\Support\Facades\Log::info('CarService: Update result:', ['updated' => $updated]);
        
        if ($updated) {
            // Add your business logic here after updating
            $this->afterUpdate($car);
        }
        
        return $updated;
    }

    /**
     * Delete Category with business logic
     */
    public function deleteWithBusinessLogic(Car $car): bool
    {
        // Add your business logic here before deleting
        $this->validateDeletion($car);
        
        $deleted = $this->delete($car);
        
        if ($deleted) {
            // Add your business logic here after deleting
            $this->afterDelete($car);
        }
        
        return $deleted;
    }

    /**
     * Get services for Category
     */
    /**
     * Validate business rules
     */
    protected function validateBusinessRules(array $data, ?Car $car = null): void
    {
        // Add your business validation logic here
        // Example: Check if required fields are present, validate relationships, etc.
    }

    /**
     * Validate deletion
     */
    protected function validateDeletion(Car $car): void
    {
        // Add your deletion validation logic here
        // Example: Check if record can be deleted, has dependencies, etc.
    }

    /**
     * After create business logic
     */
    protected function afterCreate(Car $car): void
    {
        // Add your post-creation business logic here
        // Example: Send notifications, update related records, etc.
    }

    /**
     * After update business logic
     */
    protected function afterUpdate(Car $car): void
    {
        // Add your post-update business logic here
        // Example: Send notifications, update related records, etc.
    }

    /**
     * After delete business logic
     */
    protected function afterDelete(Car $car): void
    {
        // Add your post-deletion business logic here
        // Example: Clean up related records, send notifications, etc.
    }
}