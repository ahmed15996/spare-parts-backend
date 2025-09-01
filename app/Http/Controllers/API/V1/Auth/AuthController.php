<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Requests\API\V1\Auth\ClientRegisterRequest;
use App\Http\Requests\API\V1\Auth\ProviderRegisterRequest;
use App\Http\Requests\API\V1\Auth\ProviderProfileUpdateRequest;
use App\Http\Requests\API\V1\UpdateProfileRequest;
use App\Http\Resources\API\V1\ClientResource;
use App\Http\Resources\API\V1\PersonalProfileResource;
use App\Services\AuthService;
use App\Services\ProviderProfileUpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseAuthCrontroller
{
    public function __construct(
        protected AuthService $authService,
        protected ProviderProfileUpdateService $profileUpdateService
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
        return $this->successResponse(new ClientResource($user), __('Client registered successfully'));
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

}
