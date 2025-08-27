<?php

namespace App\Services;
use App\Http\Resources\API\V1\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\ProviderRegistrationRequest;
use App\Models\User;
use App\Services\BaseService;
use App\Services\UserService;


class AuthService extends BaseService
{
    use ApiResponseTrait;

    public function __construct(private UserService $userService)
    {
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
            $registration_request = ProviderRegistrationRequest::where('phone', $data['phone'])
                ->where('status', 0)
                ->first();
            
            if ($registration_request) {
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

    public function providerRegisterRequest(array $data){
        $data['status'] = 0;
        $logo= $data['logo'] ? $data['logo'] : null;
        $commercial_number_image= $data['commercial_number_image'] ? $data['commercial_number_image'] : null;
        unset($data['logo'],$data['commercial_number_image']);
        $data['city_id'] = $data['city_id'] == 0 ? null : $data['city_id'];
        $data['brands'] = json_encode($data['brands']);
        $data['store_name'] = [
            'ar' => $data['store_name']['ar'],
            'en' => $data['store_name']['en'],
        ];
        $request = ProviderRegistrationRequest::create($data);
        if($logo){
            $request->addMediaFromRequest('logo')->toMediaCollection('logo');
        }
        if($commercial_number_image){
            $request->addMediaFromRequest('commercial_number_image')->toMediaCollection('commercial_number_image');
        }
       
        return $this->successResponse([],__('Registration request sent successfully, please wait for approval'));
    }
}
