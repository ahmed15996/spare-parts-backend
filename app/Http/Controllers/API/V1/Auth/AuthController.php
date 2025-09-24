<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Enums\DeleteAccountRequestStatus;
use App\Http\Requests\API\V1\Auth\ClientRegisterRequest;
use App\Http\Requests\API\V1\Auth\DeleteAccountRequest;
use App\Http\Requests\API\V1\Auth\ProviderRegisterRequest;
use App\Http\Requests\API\V1\Auth\ProviderProfileUpdateRequest;
use App\Http\Requests\API\V1\UpdateProfileRequest;
use App\Http\Resources\API\V1\ClientResource;
use App\Http\Resources\API\V1\DeleteAccountReasonResource;
use App\Http\Resources\API\V1\NotificationResource;
use App\Http\Resources\API\V1\PersonalProfileResource;
use App\Http\Resources\API\V1\ProviderProfileResource;
use App\Models\DeleteAccountReason;
use App\Models\DeleteAccountRequest as ModelsDeleteAccountRequest;
use App\Services\AuthService;
use App\Services\ProviderProfileUpdateService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseAuthCrontroller
{
    public function __construct(
        protected AuthService $authService,
        protected ProviderProfileUpdateService $profileUpdateService,
        protected NotificationService $notificationService
    ) {
    }
    
    //personal profile ( user data only )
    public function updateProfile(UpdateProfileRequest $request){
        $data = $request->validated();
        $updated = $this->authService->updateProfile($data, Auth::user());
        if($updated){
            return $this->successResponse(new PersonalProfileResource(Auth::user()), __('Profile updated successfully'));
        }
        return $this->errorResponse(__('Failed to update profile'));
    }
    public function getProfile(){
        $user = Auth::user();
        return $this->successResponse(new PersonalProfileResource($user), __('Profile retrieved successfully'));
    }
    public function clientRegister(ClientRegisterRequest $request){
        $data = $request->validated();
        $user = $this->authService->clientRegister($data);
        return $this->successResponse( new ClientResource($user), __('Client registered successfully'));
    }

    public function resendActiveCode(Request $request){
        $request->validate([
            'phone' => 'required|string|max:9|min:9',
        ]);
        $data = $request->all();
            $data['phone'] = $this->authService->normalizePhone($data['phone']);
        return $this->authService->resendActiveCode($data);
    }
    
    public function providerRegisterRequest (ProviderRegisterRequest $request){
        $data = $request->validated();
        return $this->authService->providerRegisterRequest($data);
        // return $this->successResponse([], 'Provider registration request sent successfully');
    }

    /**
     * Submit a provider profile update request
     * This method allows providers to request updates to their store information
     * The request will be reviewed by admin before being applied
     */
    public function providerProfileUpdateRequest(ProviderProfileUpdateRequest $request)
    {
        // Get the authenticated user
        $user = Auth::user();
        
        // Check if user is a provider
        if (!$user->hasRole('provider')) {
            return $this->errorResponse(__('Only providers can submit profile update requests'));
        }

        // Get the provider model
        $provider = $user->provider;
        
        if (!$provider) {
            return $this->errorResponse(__('Provider profile not found'));
        }

        // Get validated data
        $data = $request->validated();

        // Submit the update request
        return $this->profileUpdateService->createUpdateRequest($provider, $data);
    }

    // to return Business information for provider
    public function providerProfile(){
        $provider = Auth::user()->provider;
        return $this->successResponse(new ProviderProfileResource($provider), __('Provider profile retrieved successfully'));

    }
    public function getNotifications(Request $request){
        $notifications = $this->notificationService->getUserNotifications(Auth::user()->id,$request->per_page ?? 10);
        if($notifications->isNotEmpty()){
            return $this->paginatedResourceResponse($notifications, NotificationResource::class, __('Notifications retrieved successfully'));
        }
        return $this->paginatedResourceResponse($notifications, NotificationResource::class, __('Notifications retrieved successfully'));
    }
    public function markAsRead(Request $request){
        $notifications = $this->notificationService->markAsRead(Auth::user()->id,$request->notification_ids);
        if($notifications){
            return $this->successResponse([], __('Notifications marked as read successfully'));
        }
        return $this->errorResponse(__('Failed to mark notifications as read'));
    }
    public function deleteAccount(DeleteAccountRequest $request){
        $user = Auth::user();
        $validated = $request->validated();
        if($user->hasRole('provider')){
            $deleteAccountRequest = ModelsDeleteAccountRequest::create([
                'provider_id'=>$user->provider->id,
                'reason_id'=>$validated['reason_id'],
                'status'=>DeleteAccountRequestStatus::Pending->value,
            ]);
            return $this->successResponse([],__('Delete account request submitted successfully, the support team will contact you soon'));
        }
        
        // For clients, delete related records first to avoid foreign key constraints
        try {
            // Delete FCM tokens
            $user->fcmTokens()->delete();
            
            // Delete custom notifications
            $user->customNotifications()->delete();
            
            // Delete any other related records that might have foreign key constraints
            // Add more relationships here if needed
            
            // Finally delete the user
            $user->delete();
            
            return $this->successResponse([],__('Account deleted successfully'));
        } catch (\Exception $e) {
            return $this->errorResponse(__('Failed to delete account. Please contact support.'));
        }
    }

    public function deleteAccountReasons()
    {
        $reasons = [];
        if(Auth::user()->hasRole('provider')){
            $reasons = DeleteAccountReason::forProviders()->orderBy('reason')->get();
        }else{
            $reasons = DeleteAccountReason::forClients()->orderBy('reason')->get();
        }
        
        return $this->successResponse(DeleteAccountReasonResource::collection($reasons), __('Delete Account Reasons retrieved successfully'));
    }

}
