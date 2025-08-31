<?php

use App\Http\Controllers\API\V1\ClientController;
use App\Http\Controllers\API\V1\CarController;
use Illuminate\Support\Facades\Route;




//# Public client endpoints (no authentication required)
Route::group(['as'=>'client.','prefix'=>'client'], function () {
    Route::get('/home', [ClientController::class, 'home'])->name('home');
    Route::get('/banners/{banner_id}', [ClientController::class, 'bannserDetails'])->name('banners.show');
    
    Route::group(['prefix'=>'providers','as'=>'providers.'],function(){
        Route::get('/{id}', [ClientController::class, 'providerShow'])->name('show');
        Route::get('/{id}/products/{product_id}', [ClientController::class, 'productShow'])->name('products.show');
        Route::get('/{id}/brands', [ClientController::class, 'providerBrands'])->name('brands.show');
        Route::get('/{id}/products', [ClientController::class, 'providerProducts'])->name('products.index');
        Route::post('/search', [ClientController::class, 'search'])->name('search');
    });
});

//# Authenticated client endpoints (require authentication)
Route::group(['as'=>'client.','prefix'=>'client','middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix'=>'cars'],function(){
        Route::get('/', [CarController::class, 'index'])->name('cars.index');
        Route::get('/{id}', [CarController::class, 'show'])->name('cars.show');
        Route::post('/', [CarController::class, 'store'])->name('cars.store');
        Route::put('/{id}', [CarController::class, 'update'])->name('cars.update');
        Route::delete('/{id}', [CarController::class, 'destroy'])->name('cars.delete');
    });
});
