<?php

use App\Http\Controllers\API\V1\ClientController;
use App\Http\Controllers\API\V1\CarController;
use App\Http\Controllers\API\V1\PostController;
use App\Http\Controllers\API\V1\CommentController;
use App\Http\Controllers\API\V1\LikeController;
use App\Http\Controllers\API\V1\Client\RequestController;
use App\Http\Controllers\API\V1\ReviewController;
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
        
        // Provider reviews routes
        Route::get('/{id}/reviews', [ReviewController::class, 'getProviderReviews'])->name('reviews.index');
        Route::post('/{id}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    });
});

//# Authenticated client endpoints (require authentication)
Route::group(['as'=>'client.','prefix'=>'client','middleware' => 'auth:sanctum'], function () {
    // Reports
    \App\Http\Controllers\API\V1\ReportController::class;
    Route::post('/reports', [\App\Http\Controllers\API\V1\ReportController::class, 'store'])->name('reports.store');
    Route::group(['prefix'=>'cars'],function(){
        Route::get('/', [CarController::class, 'index'])->name('cars.index');
        Route::get('/{id}', [CarController::class, 'show'])->name('cars.show');
        Route::post('/', [CarController::class, 'store'])->name('cars.store');
        Route::put('/{id}', [CarController::class, 'update'])->name('cars.update');
        Route::delete('/{id}', [CarController::class, 'destroy'])->name('cars.delete');
    });

    Route::group(['prefix'=>'requests','as'=>'requests.'],function(){
        Route::get('/{id}', [RequestController::class, 'show'])->name('show');
        Route::post('/', [RequestController::class, 'store'])->name('store');
        Route::get('/{id}/offers/{offer_id}', [RequestController::class, 'showOffer'])->name('offers.show');
        Route::post('/{id}/filter-offers', [RequestController::class, 'filterOffers'])->name('offers.filter');
        Route::delete('/{id}/offers/{offer_id}', [RequestController::class, 'destroyOffer'])->name('offers.destroy');
    });
});
