<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//fallback routes
Route::fallback(function (){
    abort(404, 'API resource not found');
});
   
Route::post('settings', function (Request $request) {
    $request->validate([ 'entry' => 'required|string|min:5' ]); 
    return response()->json('OK');
});

/** 
 * Routes for user
 *************************************/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']); 
    Route::patch('/profile', [AuthController::class, 'update']); 
    Route::get('/logout', [AuthController::class, 'logout']); 
});



/** 
 * Routes for Admin
 *************************************/  
Route::prefix('admin')->group(function () {
    Route::post('/register', [AdminController::class, 'register']);
    Route::post('/login', [AdminController::class, 'login']);
});

Route::middleware('auth:api-admin')->group(function () {

    //admin
    Route::prefix('admin')->group(function () {
        Route::get('/profile', [AdminController::class, 'profile']); 
        Route::patch('/profile', [AdminController::class, 'updateProfile']); 
        Route::get('/logout', [AdminController::class, 'logout']); 
    });

    //modules
    Route::apiResource('modules', ModuleController::class);
  
    //roles 
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']); 
        Route::post('/', [RoleController::class, 'store']);  
        Route::get('/{id}', [RoleController::class, 'show']); 
        Route::patch('/{id}', [RoleController::class, 'update']);  
        Route::delete('/{id}', [RoleController::class, 'destroy']);   
    });
  
 
    //permissions
    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index']); 
        Route::post('/', [PermissionController::class, 'store']); 
        Route::get('/{id}', [PermissionController::class, 'show']);  
        Route::patch('/{id}', [PermissionController::class, 'update']); 
        Route::delete('/{id}', [PermissionController::class, 'destroy']); 
    }); 

});
