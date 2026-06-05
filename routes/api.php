<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LeaveController;
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

// Login route
Route::post('/login', [AuthController::class, 'login']);


Route::get('/ping', function () {
    return response()->json([
        'message' => 'API is working'
    ]);
});



Route::middleware('auth:api')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Current logged in user
    Route::get('/me', [AuthController::class, 'me']);

    // Customer CRUD
    Route::get('/customers', [CustomerController::class, 'index']);

    Route::get('/customers/{customer}', [CustomerController::class, 'show']);

    Route::post('/customers', [CustomerController::class, 'store']);

    Route::put('/customers/{customer}', [CustomerController::class, 'update']);

    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy']);


    // LEAVE CRUD
    Route::apiResource('leaves', LeaveController::class);

    // LEAVE APPROVAL
    Route::put('/leaves/{leave}/approve', [LeaveController::class, 'approve']);
    Route::put('/leaves/{leave}/reject', [LeaveController::class, 'reject']);

});
