<?php

namespace App\Services;
use App\Http\Resources\API\V1\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\ProviderRegistrationRequest;
use App\Models\User;
use App\Services\BaseService;
use App\Services\UserService;
use App\Services\ProviderRegisterationRequestService;


class AuthService extends BaseService
{
    use ApiResponseTrait;

    public function __construct(
        private UserService $userService,
        private ProviderRegisterationRequestService $providerRegistrationService
    ) {
    }

    public function login(array $data)
    {
        $user = $this->userService->findByPhone($data['phone']);
        
        if ($user) {
            if(!$user->is_active){
                return $this->errorResponse(__('Your account is stopped from adminstration'));
            }
        } else {
            // Check if there's a pending registration request for this phone
            if ($this->providerRegistrationService->hasPendingRegistration($data['phone'])) {
                return $this->errorResponse(__('You have a registration request in review. Please contact support.'));
            }
            
            $user = $this->userService->create($data);
            $user->assignRole('client');
        }

        $user->sendActiveCode();
        $user->fcmTokens()->updateOrCreate([
            'user_id' => $user->id,
        ],[
            'token' => $data['fcm_token'],
        ]);

        return $this->ok([],__('please verify your account'));
    }
    public function verifyActiveCode(array $data)
    {
        $user = User::where('phone',$data['phone'])->where('active_code',$data['code'])->first();
        
        if($user){
            $user->update([
                'is_verified' => true,
                'active_code' => null,
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;
            $first_login = false;
            if($user->first_name){
                $first_login = false;
                return $this->successResponse([
                    'token' => $token,
                    'first_login' => $first_login,
                    'user' => UserResource::make($user),
                ],__('Account verified successfully , please complete registration'));
            }else{
                $first_login = true;
                return $this->successResponse([
                    'token' => $token,
                    'first_login' => $first_login,
                    'user' => UserResource::make($user),
                ],__('Account verified successfully'));
            }
        }
        return $this->errorResponse(__('Invalid code'));
    }

    public function providerRegisterRequest(array $data)
    {
        return $this->providerRegistrationService->createRegistrationRequest($data);
    }
    public function clientRegister(array $data)
    {
        return $this->userService->create($data);
    }
}
