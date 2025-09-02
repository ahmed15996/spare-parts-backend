<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Provider;
use Filament\Notifications\Actions\Action;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
class BannerService extends BaseService
{
    protected $banner;

    public function __construct(Banner $banner)
    {
        $this->banner = $banner;
        parent::__construct($banner);
    }

    /**
     * Get Category with relationships
     */
    public function getWithScopes(array $scopes = []): Collection
    {
        $query = $this->banner->query();
        foreach ($scopes as $scope) {
            $query->$scope();
        }
        return $query->get();
    }

    /**
     * Find Category with relationships
     */
    public function findWithRelations(int $id, array $relations = []): ?Banner
    {
        return $this->banner->with($relations)->find($id);
    }
    public function getBanners(): Collection
    {
        return $this->banner->get();
    }

    /**
     * Create Category with business logic
     */
    public function createWithBusinessLogic(array $data): Banner
    {

        $image = $data['image'];
        unset($data['image']);
        
        $category = $this->create($data);
        
        // Add your business logic here after creating
        $category->addMedia($image)->toMediaCollection('image');
        $this->afterCreate($category);
        
        return $category;
    }

    /**
     * Update Category with business logic
     */
    public function updateWithBusinessLogic(Banner $banner, array $data): bool
    {
        // Add your business logic here before updating
        $this->validateBusinessRules($data, $banner);
        
        $updated = $this->update($banner, $data);
        
        if ($updated) {
            // Add your business logic here after updating
            $this->afterUpdate($banner);
        }
        
        return $updated;
    }

    /**
     * Delete Category with business logic
     */
    public function deleteWithBusinessLogic(Banner $banner): bool
    {
        // Add your business logic here before deleting
        $this->validateDeletion($banner);
        
        $deleted = $this->delete($banner);
        
        if ($deleted) {
            // Add your business logic here after deleting
                $this->afterDelete($banner);
        }
        
        return $deleted;
    }



    /**
     * Validate business rules
     */
    protected function validateBusinessRules(array $data, ?Banner $banner = null): void
    {
        // Add your business validation logic here
        // Example: Check if required fields are present, validate relationships, etc.
    }

    /**
     * Validate deletion
     */
    protected function validateDeletion(Banner $banner): void
    {
        // Add your deletion validation logic here
        // Example: Check if record can be deleted, has dependencies, etc.
    }

    /**
     * After create business logic
     */
                protected function afterCreate(Banner $banner): void
    {
        $this->sendAdminNotification(__('New banner'), __('A new banner has been created'), [
            Action::make('view')
                ->url(route('filament.admin.resources.banners.view', $banner->id))
                ->label(__('Let\'s review it'))
        ]);
    }

    /**
     * After update business logic
     */
    protected function afterUpdate(Banner $banner): void
    {
        // Add your post-update business logic here
        // Example: Send notifications, update related records, etc.
    }

    /**
     * After delete business logic
     */
    protected function afterDelete(Banner $banner): void
    {
        // Add your post-deletion business logic here
        // Example: Clean up related records, send notifications, etc.
    }

    public function getProviderBanners(Provider $provider): Collection
    {
        return $this->banner->where('provider_id', $provider->id)->get();
    }
}