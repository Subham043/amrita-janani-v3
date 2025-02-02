<?php

use App\Modules\Account\Controllers\SetupSocialAccountController;
use App\Modules\Authentication\Controllers\UserForgotPasswordController;
use App\Modules\Authentication\Controllers\UserLoginController;
use App\Modules\Authentication\Controllers\UserLogoutController;
use App\Modules\Authentication\Controllers\UserRegisterController;
use App\Modules\Authentication\Controllers\UserResetPasswordController;
use App\Modules\Authentication\Controllers\UserSocialLoginController;
use App\Modules\Account\Controllers\VerifyRegisteredUserController;
use App\Modules\Enquiries\Controllers\ContactPageController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
})->name('index');
Route::get('/content', function () {
    return view('welcome');
})->middleware(['auth', 'verified', 'social'])->name('content_dashboard');

// Route::get('/', [UserRegisterController::class, 'index', 'as' => 'home.index'])->name('index');
Route::get('/privacy-policy', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('privacy_policy');
Route::get('/about', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('about');
Route::get('/faq', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('faq');
Route::get('/darkmode', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('darkmode');
Route::get('/content_image', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('content_image');
Route::get('/content_video', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('content_video');
Route::get('/content_audio', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('content_audio');
Route::get('/content_document', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('content_document');
Route::get('/userprofile', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('userprofile');
Route::get('/display_profile_password', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('display_profile_password');
Route::get('/search_history', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('search_history');

Route::prefix('/contact-us')->group(function () {
    Route::get('/', [ContactPageController::class, 'get', 'as' => 'contact.get'])->name('contact');
    Route::post('/', [ContactPageController::class, 'post', 'as' => 'contact.post'])->name('contact_ajax');
});


Route::middleware(['guest'])->group(function () {
    Route::get('/sign-in', [UserLoginController::class, 'get', 'as' => 'login.get'])->name('login');
    Route::post('/sign-in', [UserLoginController::class, 'post', 'as' => 'login.post'])->name('signin_authenticate');
    Route::get('/sign-up', [UserRegisterController::class, 'get', 'as' => 'register.get'])->name('signup');
    Route::post('/sign-up', [UserRegisterController::class, 'post', 'as' => 'register.post'])->name('signup_store');
    Route::get('/forgot-password', [UserForgotPasswordController::class, 'get', 'as' => 'forgot_password.get'])->name('forgot_password');
    Route::post('/forgot-password', [UserForgotPasswordController::class, 'post', 'as' => 'forgot_password.post'])->name('forgot_password_request');
    Route::get('/reset-password/{token}', [UserResetPasswordController::class, 'get', 'as' => 'reset_password.get'])->name('password.reset');
    Route::post('/reset-password/{token}', [UserResetPasswordController::class, 'post', 'as' => 'reset_password.post'])->name('password.reset');
    Route::prefix('/auth')->group(function () {
        Route::prefix('/google')->group(function () {
            Route::get('/redirect', [UserSocialLoginController::class, 'google', 'as' => 'social.google'])->name('social.google');
            Route::get('/callback', [UserSocialLoginController::class, 'google_callback', 'as' => 'social.google_callback'])->name('social.google_callback');
        });
        Route::prefix('/facebook')->group(function () {
            Route::get('/redirect', [UserSocialLoginController::class, 'facebook', 'as' => 'social.facebook'])->name('social.facebook');
            Route::get('/callback', [UserSocialLoginController::class, 'facebook_callback', 'as' => 'social.facebook_callback'])->name('social.facebook_callback');
        });
    });
});

Route::prefix('/profile')->middleware(['auth'])->group(function () {
    Route::prefix('/verify')->group(function () {
        Route::get('/', [VerifyRegisteredUserController::class, 'index', 'as' => 'index'])->name('verification.notice');
        Route::post('/resend-notification', [VerifyRegisteredUserController::class, 'resend_notification', 'as' => 'resend_notification'])->middleware(['throttle:6,1'])->name('verification.send');
        Route::get('/{id}/{hash}', [VerifyRegisteredUserController::class, 'verify_email', 'as' => 'verify_email'])->middleware(['signed'])->name('verification.verify');
    });
    Route::prefix('/setup')->middleware(['verified'])->group(function () {
        Route::get('/', [SetupSocialAccountController::class, 'get', 'as' => 'profile_setup_get'])->name('profile.setup');
        Route::post('/', [SetupSocialAccountController::class, 'post', 'as' => 'profile_setup_post'])->name('profile.setup.post');
    });
});

Route::get('/sign-out', [UserLogoutController::class, 'get', 'as' => 'logout.get'])->middleware(['auth'])->name('signout');