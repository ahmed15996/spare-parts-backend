<?php

namespace App\Services;

use App\Http\Traits\ApiResponseTrait;
use App\Models\ProviderRegistrationRequest;
use App\Services\BaseService;
use Filament\Notifications\Actions\Action;

class ProviderRegisterationRequestService extends BaseService
{
    use ApiResponseTrait;

    protected $providerRegistrationRequest;

    public function __construct(ProviderRegistrationRequest $providerRegistrationRequest)
    {
        $this->providerRegistrationRequest = $providerRegistrationRequest;
        parent::__construct($providerRegistrationRequest);
    }

    /**
     * Handle provider registration request
     */
    public function createRegistrationRequest(array $data)
    {
        // Check if there's already a registration request for this phone
        $exist = ProviderRegistrationRequest::where('phone', $data['phone'])->first();
        
        if ($exist) {
            if ($exist->status == 0) {
                return $this->errorResponse(__('You have a registration request in review, please wait for approval'));
            } else {
                return $this->errorResponse(__('This phone number is already registered'));
            }
        }

        // Set default status as pending (0)
        $data['status'] = 0;
        
        // Handle media files
        $logo = $data['logo'] ?? null;
        $commercial_number_image = $data['commercial_number_image'] ?? null;
        unset($data['logo'], $data['commercial_number_image']);
        
        // Process brands array
        $data['brands'] = json_encode($data['brands']);
        
        // Process store_name translations
        $data['store_name'] = [
            'ar' => $data['store_name']['ar'],
            'en' => $data['store_name']['en'] ?? null,
        ];

        // Create the registration request
        $request = ProviderRegistrationRequest::create($data);

        // Handle media uploads
        if ($logo) {
            $request->addMediaFromRequest('logo')->toMediaCollection('logo');
        }
        
        if ($commercial_number_image) {
            $request->addMediaFromRequest('commercial_number_image')->toMediaCollection('commercial_number_image');
        }

        $this->afterCreate($request);
        return $this->successResponse([], __('Registration request sent successfully, please wait for approval'));
    }

    /**
     * Check if phone has pending registration
     */
    public function hasPendingRegistration(string $phone): bool
    {
        return ProviderRegistrationRequest::where('phone', $phone)
            ->where('status', 0)
            ->exists();
    }

    /**
     * Get pending registration by phone
     */
    public function getPendingRegistrationByPhone(string $phone): ?ProviderRegistrationRequest
    {
        return ProviderRegistrationRequest::where('phone', $phone)
            ->where('status', 0)
            ->first();
    }

    /**
     * Approve registration request and create provider
     */
    public function approveRegistration(ProviderRegistrationRequest $request): array
    {
        // This method can be implemented later for handling approval logic
        // For now, it's handled in the Filament resource
        return ['status' => 'success'];
    }

    /**
     * Reject registration request
     */
    public function rejectRegistration(ProviderRegistrationRequest $request, string $reason = null): bool
    {
        return $request->update([
            'status' => 2, // Rejected status
            'rejection_reason' => $reason
        ]);
    }

    /**
     * Get registration requests with filters
     */
    public function getRegistrationRequests(array $filters = [])
    {
        $query = ProviderRegistrationRequest::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        return $query->with(['city', 'category'])->get();
    }

    /**
     * Validate business rules for registration
     */
    protected function validateBusinessRules(array $data): void
    {
        // Add specific validation rules for provider registration
        // Example: Check if category exists, city exists, etc.
    }

    /**
     * After create business logic
     */
    protected function afterCreate(ProviderRegistrationRequest $request): void
    {
       $this->sendAdminNotification('تسجيل طلب تسجيل مزود', 'تم إرسال طلب تسجيل مزود جديد', [
         Action::make('view')
            ->url(route('filament.admin.resources.provider-registration-requests.view', $request->id))
            ->label(__('View'))
            ->icon('heroicon-o-eye')
            ->color('primary'),
       ]);
    }
}