<?php

use App\Http\Controllers\API\V1\ClientController;
use Illuminate\Support\Facades\Route;




//# General endpoints 
Route::group(['as'=>'client.','prefix'=>'client','middleware' => ['guest']], function () {
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
