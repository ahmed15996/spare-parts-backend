<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Requests\API\V1\Auth\ClientRegisterRequest;
use App\Http\Requests\API\V1\Auth\ProviderRegisterRequest;
use App\Http\Requests\API\V1\UpdateProfileRequest;
use App\Http\Resources\API\V1\ClientResource;
use App\Http\Resources\API\V1\PersonalProfileResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseAuthCrontroller
{
    public function __construct(protected AuthService $authService)
    {
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




}
