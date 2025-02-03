<?php

use App\Modules\Authentication\Controllers\AdminForgotPasswordController;
use App\Modules\Authentication\Controllers\AdminLoginController;
use App\Modules\Authentication\Controllers\AdminLogoutController;
use App\Modules\Authentication\Controllers\AdminResetPasswordController;
use Illuminate\Support\Facades\Route;


Route::prefix('/admin')->group(function () {
    Route::prefix('/auth')->middleware(['guest'])->group(function () {
        Route::get('/login', [AdminLoginController::class, 'get', 'as' => 'admin.login'])->name('signin');
        Route::post('/authenticate', [AdminLoginController::class, 'post', 'as' => 'admin.authenticate'])->name('authenticate');
        Route::get('/forgot-password', [AdminForgotPasswordController::class, 'get', 'as' => 'admin.forgot_password'])->name('forgotPassword');
        Route::post('/forgot-password', [AdminForgotPasswordController::class, 'post', 'as' => 'admin.requestForgotPassword'])->name('requestForgotPassword');
        Route::prefix('/reset-password/{token}')->middleware(['signed'])->group(function () {
            Route::get('/', [AdminResetPasswordController::class, 'get', 'as' => 'admin.reset_password'])->name('reset_password');
            Route::post('/', [AdminResetPasswordController::class, 'post', 'as' => 'admin.requestResetPassword'])->name('requestResetPassword');
        });
    });
    
    Route::middleware(['auth:admin', 'is_admin'])->group(function () {
        Route::get('/dashboard', function () {
            return view('welcome');
        })->name('dashboard');
        Route::get('/logout', [AdminLogoutController::class, 'get', 'as' => 'admin.logout'])->name('logout');
    });
});
