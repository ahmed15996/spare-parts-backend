<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Car\StoreCarRequest;
use App\Http\Requests\API\V1\Car\UpdateCarRequest;
use App\Http\Resources\API\V1\CarResource;
use App\Services\CarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CarController extends Controller
{
    public function __construct(protected CarService $carService)
    {
    }

    /**
     * Get all cars for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $cars = $this->carService->getWithScopes()->where('user_id', $user->id);

        if($cars->isEmpty()){
            return $this->successResponse([], __('No cars found'));
        }
        return $this->successResponse(
            CarResource::collection($cars),
         __('Cars fetched successfully'));
    }

    /**
     * Get a specific car by ID
     */
    public function show(Request $request, $id)
    {
        $user = Auth::user();
        $car = $this->carService->findWithRelations($id, ['brandModel', 'user']);
        
        if(!$car){
            return $this->errorResponse(__('Car not found'), 404);
        }
        
        // Ensure user can only view their own cars
        if($car->user_id !== $user->id){
            return $this->errorResponse(__('Unauthorized'), 403);
        }
        
        return $this->successResponse(
            CarResource::make($car),
         __('Car fetched successfully'));
    }

    /**
     * Create a new car
     */
    public function store(StoreCarRequest $request)
    {
        $user = Auth::user();
        
        $carData = $request->validated();
        $carData['user_id'] = $user->id;
        
        $car = $this->carService->createWithBusinessLogic($carData);
        
        return $this->successResponse(
            CarResource::make($car),
         __('Car created successfully'));
    }

    /**
     * Update an existing car
     */
    public function update(UpdateCarRequest $request, $id)
    {
        $user = Auth::user();
        $car = $this->carService->findWithRelations($id);
        
        if(!$car){
            return $this->errorResponse(__('Car not found'), 404);
        }
        
        // Ensure user can only update their own cars
        if($car->user_id !== $user->id){
            return $this->errorResponse(__('Unauthorized'), 403);
        }
        
        $validatedData = $request->validated();
        
        // Debug: Log the data being updated
        Log::info('Updating car with data:', $validatedData);
        Log::info('Car before update:', $car->toArray());
        
        $updated = $this->carService->updateWithBusinessLogic($car, $validatedData);
        
        if(!$updated){
            return $this->errorResponse(__('Failed to update car'), 500);
        }
        
        // Refresh the car model to get updated data
        $car->refresh();
        
        return $this->successResponse(
            CarResource::make($car),
       __('Car updated successfully'));
    }

    /**
     * Delete a car
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $car = $this->carService->findWithRelations($id);
        
        if(!$car){
            return $this->errorResponse(__('Car not found'), 404);
        }
        
        // Ensure user can only delete their own cars
        if($car->user_id !== $user->id){
            return $this->errorResponse(__('Unauthorized'), 403);
        }
        
        $deleted = $this->carService->deleteWithBusinessLogic($car);
        
        if(!$deleted){
            return $this->errorResponse(__('Failed to delete car'), 500);
        }
        
        return $this->successResponse([], __('Car deleted successfully'));
    }
}
