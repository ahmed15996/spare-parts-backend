<?php

namespace App\Services;

use App\Http\Traits\ApiResponseTrait;
use App\Models\Provider;
use App\Models\ProviderProfileUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;
use Filament\Notifications\Actions\Action;

class ProviderProfileUpdateService extends BaseService
{
    use ApiResponseTrait;

    protected $providerProfileUpdateRequest;

    public function __construct(ProviderProfileUpdateRequest $providerProfileUpdateRequest)
    {
        $this->providerProfileUpdateRequest = $providerProfileUpdateRequest;
        parent::__construct($providerProfileUpdateRequest);
    }

    /**
     * Create a profile update request for a provider
     */
    public function createUpdateRequest(Provider $provider, array $data)
    {
        try {
            // Check if there's already a pending update request
            $existingRequest = ProviderProfileUpdateRequest::where('provider_id', $provider->id)
                ->where('status', ProviderProfileUpdateRequest::STATUS_PENDING)
                ->first();
            
            if ($existingRequest) {
                return $this->errorResponse(__('You already have a pending profile update request. Please wait for admin approval.'));
            }

            // Handle media files
            $logo = $data['logo'] ?? null;
            $commercial_number_image = $data['commercial_number_image'] ?? null;
            unset($data['logo'], $data['commercial_number_image']);

                    // Prepare data for storage - only include fields that are provided
        $updateData = [
            'provider_id' => $provider->id,
            'status' => ProviderProfileUpdateRequest::STATUS_PENDING,
        ];
        
        // Only add fields that are provided
        $fieldsToCheck = ['description', 'city_id', 'category_id', 'commercial_number', 'location', 'brands', 'lat', 'long', 'address'];
        foreach ($fieldsToCheck as $field) {
            if (isset($data[$field]) && $data[$field] !== null) {
                $updateData[$field] = $data[$field];
            }
        }
        
        // Process store_name if provided
        if (isset($data['store_name']) && !empty($data['store_name'])) {
            $updateData['store_name'] = $data['store_name'];
        }

                    // Create the update request
        $updateRequest = ProviderProfileUpdateRequest::create($updateData);

            // Handle media uploads
            if ($logo) {
                $updateRequest->addMedia($logo)
                    ->toMediaCollection('logo');
            }

            if ($commercial_number_image) {
                $updateRequest->addMedia($commercial_number_image)
                    ->toMediaCollection('commercial_number_image');
            }

            // Send notification to admins
            $this->notifyAdminsOfPendingRequest($updateRequest);

            return $this->successResponse(
                [],
                __('Profile update request submitted successfully. You will be notified once it is reviewed.'),
            );

        } catch (Exception $e) {
            return $this->errorResponse(__('Failed to submit profile update request: ') . $e->getMessage());
        }
    }

    /**
     * Approve a profile update request
     */
    public function approveUpdateRequest(ProviderProfileUpdateRequest $updateRequest, User $admin)
    {
        if (!$updateRequest->isPending()) {
            throw new Exception('This request has already been processed.');
        }

        DB::transaction(function () use ($updateRequest, $admin) {
            // Get the provider
            $provider = $updateRequest->provider;

            // Prepare update data - only include non-null fields
            $providerUpdateData = [];
            
            if ($updateRequest->store_name !== null) {
                // Convert simple string to translatable format for Provider model
                $providerUpdateData['store_name'] = $updateRequest->store_name;
            }
            if ($updateRequest->description !== null) {
                $providerUpdateData['description'] = $updateRequest->description;
            }
            if ($updateRequest->city_id !== null) {
                $providerUpdateData['city_id'] = $updateRequest->city_id;
            }
            if ($updateRequest->commercial_number !== null) {
                $providerUpdateData['commercial_number'] = $updateRequest->commercial_number;
            }
            if ($updateRequest->location !== null) {
                $providerUpdateData['location'] = $updateRequest->location;
            }
            if ($updateRequest->category_id !== null) {
                $providerUpdateData['category_id'] = $updateRequest->category_id;
            }

            // Update provider data if there are changes
            if (!empty($providerUpdateData)) {
                $provider->update($providerUpdateData);
            }

            // Handle lat, long, address changes - update in users table
            $userUpdateData = [];
            if ($updateRequest->lat !== null) {
                $userUpdateData['lat'] = $updateRequest->lat;
            }
            if ($updateRequest->long !== null) {
                $userUpdateData['long'] = $updateRequest->long;
            }
            if ($updateRequest->address !== null) {
                $userUpdateData['address'] = $updateRequest->address;
            }

            // Update user data if there are location changes
            if (!empty($userUpdateData)) {
                $provider->user->update($userUpdateData);
            }

            // Update brands relationship if provided
            if ($updateRequest->brands !== null) {
                // The brands attribute is automatically converted to array by the model accessor
                $brands = is_array($updateRequest->brands) ? $updateRequest->brands : [];
                $provider->brands()->sync($brands);
            }

            // Copy media files from request to provider
            $this->copyMediaToProvider($updateRequest, $provider);

            // Update the request status
            $updateRequest->update([
                'status' => ProviderProfileUpdateRequest::STATUS_APPROVED,
                'processed_at' => now(),
                'processed_by' => $admin->id,
            ]);

            // Notify the provider
            $this->notifyProviderOfApproval($provider);
        });

        return true;
    }

    /**
     * Reject a profile update request
     */
    public function rejectUpdateRequest(ProviderProfileUpdateRequest $updateRequest, User $admin, string $reason = null)
    {
        if (!$updateRequest->isPending()) {
            throw new Exception('This request has already been processed.');
        }

        $updateRequest->update([
            'status' => ProviderProfileUpdateRequest::STATUS_REJECTED,
            'processed_at' => now(),
            'processed_by' => $admin->id,
            'admin_notes' => $reason,
        ]);

        // Notify the provider
        $this->notifyProviderOfRejection($updateRequest->provider, $reason);

        return true;
    }

    /**
     * Get pending update requests count
     */
    public function getPendingRequestsCount()
    {
        return ProviderProfileUpdateRequest::pending()->count();
    }

    /**
     * Get provider's update request history
     */
    public function getProviderUpdateHistory(Provider $provider)
    {
        return ProviderProfileUpdateRequest::where('provider_id', $provider->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Copy media from update request to provider
     */
    protected function copyMediaToProvider(ProviderProfileUpdateRequest $updateRequest, Provider $provider)
    {
        // Copy logo
        if ($updateRequest->getFirstMedia('logo')) {
            // Clear existing logo
            $provider->clearMediaCollection('logo');
            // Copy new logo
            $updateRequest->getFirstMedia('logo')->copy($provider, 'logo');
        }

        // Copy commercial number image
        if ($updateRequest->getFirstMedia('commercial_number_image')) {
            // Clear existing commercial number image
            $provider->clearMediaCollection('commercial_number_image');
            // Copy new commercial number image
            $updateRequest->getFirstMedia('commercial_number_image')->copy($provider, 'commercial_number_image');
        }
    }

    /**
     * Notify admins of pending request
     */
    protected function notifyAdminsOfPendingRequest(ProviderProfileUpdateRequest $updateRequest)
    {
       
       $this->sendAdminNotification('تسجيل طلب تحديث ملف المزود', 'تم إرسال طلب تحديث ملف المزود جديد', [
         Action::make('view')
            ->url(route('filament.admin.resources.provider-profile-update-requests.view', $updateRequest->id))
            ->label(__('View'))
            ->icon('heroicon-o-eye')
            ->color('primary'),
       ]);
    
    }

    /**
     * Notify provider of approval
     */
    protected function notifyProviderOfApproval(Provider $provider)
    {
        $provider->user->customNotifications()->create([
            'title'=>[
                'ar'=>'تمت الموافقة على طلب تحديث ملف المزود',
                'en'=>'Provider profile update request approved',
            ],
            'body'=>[
                'ar'=>'تمت الموافقة على طلب تحديث ملف المزود',
                'en'=>'Provider profile update request approved',
            ],
            'metadata'=>[]

        ]);
    }

    /**
     * Notify provider of rejection
     */
    protected function notifyProviderOfRejection(Provider $provider, ?string $reason)
    {
        $provider->user->customNotifications()->create([
            'title'=>[
                'ar'=>'تم رفض طلب تحديث ملف المزود',
                'en'=>'Provider profile update request rejected',
            ],
            'body'=>[
                'ar'=>'تم رفض طلب تحديث ملف المزود بسبب السبب التالي: '.$reason,
                'en'=>'Provider profile update request rejected with the following reason: '.$reason,
            ],
            'metadata'=>[]

        ]);
    }
}
