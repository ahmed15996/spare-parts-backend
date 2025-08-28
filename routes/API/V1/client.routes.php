<?php

use App\Http\Controllers\API\V1\ClientController;
use Illuminate\Support\Facades\Route;




//# General endpoints 
Route::group(['as'=>'client.','prefix'=>'client','middleware' => ['auth:sanctum']], function () {
    Route::get('/home', [ClientController::class, 'home'])->name('home');
    Route::post('/search', [ClientController::class, 'search'])->name('search');
    });
