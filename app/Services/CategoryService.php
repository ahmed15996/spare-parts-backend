<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CategoryService extends BaseService
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
        parent::__construct($category);
    }

    /**
     * Get Category with relationships
     */
    public function getWithScopes(array $scopes = []): Collection
    {
        $query = $this->category->query();
        foreach ($scopes as $scope) {
            $query->$scope();
        }
        return $query->get();
    }

    /**
     * Find Category with relationships
     */
    public function findWithRelations(int $id, array $relations = []): ?Category
    {
        return $this->category->with($relations)->find($id);
    }

    /**
     * Create Category with business logic
     */
    public function createWithBusinessLogic(array $data): Category
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
    public function updateWithBusinessLogic(Category $category, array $data): bool
    {
        // Add your business logic here before updating
        $this->validateBusinessRules($data, $category);
        
        $updated = $this->update($category, $data);
        
        if ($updated) {
            // Add your business logic here after updating
            $this->afterUpdate($category);
        }
        
        return $updated;
    }

    /**
     * Delete Category with business logic
     */
    public function deleteWithBusinessLogic(Category $category): bool
    {
        // Add your business logic here before deleting
        $this->validateDeletion($category);
        
        $deleted = $this->delete($category);
        
        if ($deleted) {
            // Add your business logic here after deleting
            $this->afterDelete($category);
        }
        
        return $deleted;
    }

    /**
     * Get services for Category
     */
    public function getServices(Category $category): Collection
    {
        return $category->services;
    }

    /**
     * Add services to Category
     */
    public function addService(Category $category, array $data): Model
    {
        return $category->services()->create($data);
    }

    /**
     * Validate business rules
     */
    protected function validateBusinessRules(array $data, ?Category $category = null): void
    {
        // Add your business validation logic here
        // Example: Check if required fields are present, validate relationships, etc.
    }

    /**
     * Validate deletion
     */
    protected function validateDeletion(Category $category): void
    {
        // Add your deletion validation logic here
        // Example: Check if record can be deleted, has dependencies, etc.
    }

    /**
     * After create business logic
     */
    protected function afterCreate(Category $category): void
    {
        // Add your post-creation business logic here
        // Example: Send notifications, update related records, etc.
    }

    /**
     * After update business logic
     */
    protected function afterUpdate(Category $category): void
    {
        // Add your post-update business logic here
        // Example: Send notifications, update related records, etc.
    }

    /**
     * After delete business logic
     */
    protected function afterDelete(Category $category): void
    {
        // Add your post-deletion business logic here
        // Example: Clean up related records, send notifications, etc.
    }
}