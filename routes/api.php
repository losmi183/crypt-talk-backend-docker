<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\PusherAuthController;
use App\Http\Controllers\ConversatonController;


Route::group(['prefix' => 'auth'], function () {
    
    // Send verification email
    Route::post('/register', [AuthController::class, 'register']);
    // Send verification email - user already created but unactive
    Route::post('/resend-verify-email', [AuthController::class, 'resendVerifyEmail']);
    // Decrypt token, activate user, redirect to login
    Route::get('/verify-email', [AuthController::class, 'verifyEmail']);
    
    
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class,'refresh']);
    
    Route::post('/logout', action: [AuthController::class, 'logout']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    
    Route::post('/google-login', [AuthController::class, 'googleLogin']);
    Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback']);    
    
});

Route::group(['middleware' => 'jwt', 'prefix' => 'auth'], function () {
    Route::get('/whoami', [AuthController::class, 'whoami']);
    Route::post('/profile-update', [AuthController::class, 'profileUpdate']);
    Route::post('/password-update', [AuthController::class, 'passwordUpdate']);
});

Route::group(['middleware' => 'jwt', 'prefix' => 'user'], function () {
    Route::post('/search', [UserController::class, 'search']);
    Route::get('/show/{id}', [UserController::class, 'show']);
    Route::get('/edit', [UserController::class, 'edit']);
    Route::post('/update', [UserController::class, 'update']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
});

Route::post('/pusher/auth', [PusherAuthController::class, 'authenticate'])->middleware('jwt');
Route::group(['middleware' => 'jwt', 'prefix' => 'conversation'], routes: function () {
    Route::get('/index', [ConversatonController::class, 'index']);
    Route::post('/start-conversation', [ConversatonController::class, 'startConversation']);
    Route::post('/show', [ConversatonController::class, 'show']);
    Route::post('/send-message', [ConversatonController::class, 'sendMessage']);
    Route::post('/send-attachment', [AttachmentController::class, 'sendAttachment']);
    Route::post('/seen', [ConversatonController::class, 'seen']);
    Route::post('/mark-as-read', [ConversatonController::class, 'markAsRead']);

    });
Route::get('/media/{type}/{file}', [AttachmentController::class, 'show']);
