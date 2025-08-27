<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BrandService extends BaseService
{
    protected $brand;

    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
        parent::__construct($brand);
    }

    /**
     * Get Category with relationships
     */
    public function getWithScopes(array $scopes = []): Collection
    {
        $query = $this->brand->query();
        foreach ($scopes as $scope) {
            $query->$scope();
        }
        return $query->get();
    }

    /**
     * Find Category with relationships
     */
    public function findWithRelations(int $id, array $relations = []): ?Brand
    {
        return $this->brand->with($relations)->find($id);
    }
    public function getModels(): Collection
    {
        return $this->brand->models;
    }

    /**
     * Create Category with business logic
     */
    public function createWithBusinessLogic(array $data): Brand
    {
        // Add your business logic here before creating
        $this->validateBusinessRules($data);
        
        $category = $this->create($data);
        
        // Add your business logic here after creating
        $this->afterCreate($category);
        
        return $category;
    }

    /**
     * Update Category with business logic
     */
    public function updateWithBusinessLogic(Brand $brand, array $data): bool
    {
        // Add your business logic here before updating
        $this->validateBusinessRules($data, $brand);
        
        $updated = $this->update($brand, $data);
        
        if ($updated) {
            // Add your business logic here after updating
            $this->afterUpdate($brand);
        }
        
        return $updated;
    }

    /**
     * Delete Category with business logic
     */
    public function deleteWithBusinessLogic(Brand $brand): bool
    {
        // Add your business logic here before deleting
        $this->validateDeletion($brand);
        
        $deleted = $this->delete($brand);
        
        if ($deleted) {
            // Add your business logic here after deleting
            $this->afterDelete($brand);
        }
        
        return $deleted;
    }



    /**
     * Validate business rules
     */
    protected function validateBusinessRules(array $data, ?Brand $brand = null): void
    {
        // Add your business validation logic here
        // Example: Check if required fields are present, validate relationships, etc.
    }

    /**
     * Validate deletion
     */
    protected function validateDeletion(Brand $brand): void
    {
        // Add your deletion validation logic here
        // Example: Check if record can be deleted, has dependencies, etc.
    }

    /**
     * After create business logic
     */
    protected function afterCreate(Brand $brand): void
    {
        // Add your post-creation business logic here
        // Example: Send notifications, update related records, etc.
    }

    /**
     * After update business logic
     */
            protected function afterUpdate(Brand $brand): void
    {
        // Add your post-update business logic here
        // Example: Send notifications, update related records, etc.
    }

    /**
     * After delete business logic
     */
    protected function afterDelete(Brand $brand): void
    {
        // Add your post-deletion business logic here
        // Example: Clean up related records, send notifications, etc.
    }
}