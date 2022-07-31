<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\InsuranceController;

Route::post('login', [AuthController::class, 'signin']);
Route::post('register', [AuthController::class, 'signup']);

Route::middleware('auth:sanctum')->group( function () { 
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/getUsers', [AuthController::class, 'getUsers']);

    // customers
    // Route::get('/customers', [CustomerController::class, 'getAll']);
    // Route::get('/customers/{id}', [CustomerController::class, 'getId']);
    // Route::post('/customers', [CustomerController::class, 'create']);
    // Route::put('/customers/{id}', [CustomerController::class, 'update']);
    // Route::delete('/customers/{id}', [CustomerController::class, 'delete']);
    // Route::get('/customers/search/{name}', [CustomerController::class, 'search']);
    Route::post('/customers', [CustomerController::class, 'index']);
    
    // insurances
    // Route::get('/insurances', [InsuranceController::class, 'getAll']);
    // Route::get('/insurances/{id}', [InsuranceController::class, 'getId']);
    // Route::post('/insurances', [InsuranceController::class, 'create']);
    // Route::put('/insurances/{id}', [InsuranceController::class, 'update']);
    // Route::delete('/insurances/{id}', [InsuranceController::class, 'delete']);
    // Route::post('/insurances', [InsuranceController::class, 'index']);
});