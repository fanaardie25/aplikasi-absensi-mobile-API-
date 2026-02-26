<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\SchoolClassController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//auth
Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

//class 
Route::group(['prefix' => 'class'], function () {
    Route::get('/', [SchoolClassController::class, 'index']);
    Route::post('/', [SchoolClassController::class, 'store']);
    Route::get('/{id}', [SchoolClassController::class, 'show']);
    Route::put('/{id}', [SchoolClassController::class, 'update']);
    Route::delete('/{id}', [SchoolClassController::class, 'destroy']);
});