<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These routes are stateless and return JSON responses.
| JWT authentication will be added later.
*/

/*
|-----------------------------------------
| TEST ROUTE (check API is working)
|-----------------------------------------
*/



Route::get('/ping', function () {
    return response()->json([
        'message' => 'API is working'
    ]);
});

// Get all customers
Route::get('/customers', [CustomerController::class, 'index']);

// Get single customer by ID
Route::get('/customers/{id}', [CustomerController::class, 'show']);

// Create customer
Route::post('/customers', [CustomerController::class, 'store']);

// Update customer
Route::put('/customers/{id}', [CustomerController::class, 'update']);

// Delete customer
Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);
