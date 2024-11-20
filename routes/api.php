<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\RealisationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserTokenController;

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register']);
Route::post('createToken', [UserTokenController::class, 'createToken']);
Route::post('verifyToken', [UserTokenController::class, 'verifyToken']);
Route::post('passwordOrEmailChange',[UserController::class, 'passwordOrEmailChange']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('users', UserController::class)->except(['store']);
    Route::apiResource('tasks', TaskController::class);
    Route::put('realisations/{id}', [RealisationController::class,'update']);
    Route::get('realisations/byDate/', [RealisationController::class,'showByDate']);
    Route::get('dashboard', [DashboardController::class,'index']);
});