<?php

namespace App\Http\Controllers\API\V1;

use App\Enums\Users\ProfileStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Brand\BrandCompleteProfileRequest;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function __construct(private ClientService $clientService){
        
    }

    public function completeProfile(BrandCompleteProfileRequest $request){
        try{
            $client = Auth::user()->client;
            if (!$client) {
                return $this->notFound(__('No Profile Found'));
            }
            if(Auth::user()->profile_status == ProfileStatus::Accepted){
                return $this->errorResponse(__('Profile is already completed'),400);
            }
            $client = $this->clientService->brandCompleteProfile($client,$request->validated(), ProfileStatus::Accepted);
            return $this->created([], __('Profile is completed successfully'));
        } catch (\Exception $e) {
            Log::debug($e);
           return $this->errorResponse(__('Failed to complete profile'),400);
        }
    }
}
    