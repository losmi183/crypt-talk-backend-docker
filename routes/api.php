<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// Send verification email
Route::post('/auth/register', [AuthController::class, 'register']);
// Send verification email - user already created but unactive
Route::post('/auth/resend-verify-email', [AuthController::class, 'resendVerifyEmail']);

Route::get('/auth/verify-email', [AuthController::class, 'verifyEmail']);


Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class,'refresh']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth/forgot-password', [UserController::class, 'forgotPassword']);

Route::post('/auth/google-login', [AuthController::class, 'googleLogin']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);



Route::group(['middleware' => 'jwt', 'prefix' => 'auth'], function () {
    Route::get('/whoami', [AuthController::class, 'whoami']);
});

Route::group(['middleware' => 'jwt', 'prefix' => 'user'], function () {
    Route::post('/search', [UserController::class, 'search']);
    Route::get('/show/{id}', [UserController::class, 'show']);
    Route::get('/edit', [UserController::class, 'edit']);
    Route::post('/update', [UserController::class, 'update']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
});
