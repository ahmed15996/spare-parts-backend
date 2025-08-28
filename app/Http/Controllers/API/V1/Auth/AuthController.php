<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Requests\API\V1\Auth\ClientRegisterRequest;
use App\Http\Requests\API\V1\Auth\ProviderRegisterRequest;
use App\Http\Resources\API\V1\ClientResource;
use App\Services\AuthService;
use Illuminate\Http\Request;


class AuthController extends BaseAuthCrontroller
{
    public function __construct(protected AuthService $authService)
    {
    }
    // User Register (Agency , Brand)
    public function providerRegisterRequest (ProviderRegisterRequest $request){
        $data = $request->validated();
        return $this->authService->providerRegisterRequest($data);
        // return $this->successResponse([], 'Provider registration request sent successfully');
    }

    public function clientRegister(ClientRegisterRequest $request){
        $data = $request->validated();
        $user = $this->authService->clientRegister($data);
        return $this->successResponse(new ClientResource($user), __('Client registered successfully'));
    }



}
