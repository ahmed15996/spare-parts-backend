<?php

use App\Http\Controllers\API\V1\ClientController;
use Illuminate\Support\Facades\Route;




//# General endpoints 
Route::group(['as'=>'client.','prefix'=>'client','middleware' => ['auth:sanctum']], function () {
    Route::get('/home', [ClientController::class, 'home'])->name('home');
    Route::group(['prefix'=>'providers'],function(){
        Route::get('/{provider}', [ClientController::class, 'providerShow'])->name('show');
        Route::post('/search', [ClientController::class, 'search'])->name('search');


    });
    });
