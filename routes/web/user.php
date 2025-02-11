<?php

use App\Modules\Account\Controllers\SetupSocialAccountController;
use App\Modules\Authentication\Controllers\UserForgotPasswordController;
use App\Modules\Authentication\Controllers\UserLoginController;
use App\Modules\Authentication\Controllers\UserLogoutController;
use App\Modules\Authentication\Controllers\UserRegisterController;
use App\Modules\Authentication\Controllers\UserResetPasswordController;
use App\Modules\Authentication\Controllers\UserSocialLoginController;
use App\Modules\Account\Controllers\VerifyRegisteredUserController;
use App\Modules\Web\Controllers\AboutPageController;
use App\Modules\Web\Controllers\AudioContentController;
use App\Modules\Web\Controllers\ContactPageController;
use App\Modules\Web\Controllers\DarkModeController;
use App\Modules\Web\Controllers\DocumentContentController;
use App\Modules\Web\Controllers\FAQPageController;
use App\Modules\Web\Controllers\HomePageController;
use App\Modules\Web\Controllers\ImageContentController;
use App\Modules\Web\Controllers\PrivacyPolicyPageController;
use App\Modules\Web\Controllers\VideoContentController;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomePageController::class, 'get', 'as' => 'home.index'])->name('index');
Route::get('/privacy-policy', [PrivacyPolicyPageController::class, 'get', 'as' => 'privacy_policy.index'])->name('privacy_policy');
Route::get('/about', [AboutPageController::class, 'get', 'as' => 'privacy_policy.index'])->name('about');
Route::get('/faq', [FAQPageController::class, 'get', 'as' => 'privacy_policy.index'])->name('faq');

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
    Route::prefix('/reset-password/{token}')->middleware(['signed'])->group(function () {
        Route::get('/', [UserResetPasswordController::class, 'get', 'as' => 'reset_password.get'])->name('password.reset');
        Route::post('/', [UserResetPasswordController::class, 'post', 'as' => 'reset_password.post'])->name('password.reset');
    });
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

Route::middleware(['auth'])->group(function () {
    Route::prefix('/profile')->group(function () {
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

    Route::prefix('/')->middleware(['verified', 'password_is_set', 'is_not_blocked'])->group(function () {
        Route::get('/content', function () {
            return view('welcome');
        })->name('content_dashboard');

        Route::get('/darkmode', [DarkModeController::class, 'get', 'as' => 'darkmode.index'])->name('darkmode');
        Route::get('/content_image', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('content_image');
        Route::get('/content_video', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('content_video');
        Route::get('/content_audio', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('content_audio');
        Route::get('/content_document', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('content_document');
        Route::get('/userprofile', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('userprofile');
        Route::get('/display_profile_password', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('display_profile_password');
        Route::get('/search_history', [UserRegisterController::class, 'index', 'as' => 'privacy_policy.index'])->name('search_history');

        Route::prefix('/content')->group(function () {
            // Route::get('/', [DashboardPageController::class, 'index', 'as' => 'content.dashboard'])->name('content_dashboard');
            // Route::post('/search-query', [DashboardPageController::class, 'search_query', 'as' => 'content.search_query'])->name('content_search_query');
    
            Route::prefix('/image')->group(function () {
                Route::get('/', [ImageContentController::class, 'index', 'as' => 'content.image'])->name('content_image');
                Route::get('/{uuid}', [ImageContentController::class, 'view', 'as' => 'content.image_view'])->name('content_image_view');
                Route::get('/{uuid}/make-favourite', [ImageContentController::class, 'makeFavourite', 'as' => 'content.image_makeFavourite'])->name('content_image_makeFavourite');
                Route::post('/{uuid}/request-access', [ImageContentController::class, 'requestAccess', 'as' => 'content.image_requestAccess'])->name('content_image_requestAccess');
                Route::post('/{uuid}/report', [ImageContentController::class, 'report', 'as' => 'content.image_report'])->name('content_image_report');
                Route::post('/search-query', [ImageContentController::class, 'search_query', 'as' => 'content.image_search_query'])->name('content_image_search_query');
                Route::get('/file/{uuid}', [ImageContentController::class, 'imageFile', 'as' => 'image.imageFile'])->name('content_image_file');
                Route::get('/file/{uuid}/thumbnail', [ImageContentController::class, 'thumbnail', 'as' => 'image.thumbnail'])->name('content_image_thumbnail');
            });
    
            Route::prefix('/document')->group(function () {
                Route::get('/', [DocumentContentController::class, 'index', 'as' => 'content.document'])->name('content_document');
                Route::get('/{uuid}', [DocumentContentController::class, 'view', 'as' => 'content.document_view'])->name('content_document_view');
                Route::get('/{uuid}/make-favourite', [DocumentContentController::class, 'makeFavourite', 'as' => 'content.document_makeFavourite'])->name('content_document_makeFavourite');
                Route::post('/{uuid}/request-access', [DocumentContentController::class, 'requestAccess', 'as' => 'content.document_requestAccess'])->name('content_document_requestAccess');
                Route::post('/{uuid}/report', [DocumentContentController::class, 'report', 'as' => 'content.document_report'])->name('content_document_report');
                Route::post('/search-query', [DocumentContentController::class, 'search_query', 'as' => 'content.document_search_query'])->name('content_document_search_query');
                Route::get('/file/{uuid}', [DocumentContentController::class, 'documentFile', 'as' => 'document.documentFile'])->name('content_document_file');
                Route::get('/file-reader/{uuid}', [DocumentContentController::class, 'documentReader', 'as' => 'document.documentReader'])->name('content_document_reader');
            });
    
            Route::prefix('/audio')->group(function () {
                Route::get('/', [AudioContentController::class, 'index', 'as' => 'content.audio'])->name('content_audio');
                Route::get('/{uuid}', [AudioContentController::class, 'view', 'as' => 'content.audio_view'])->name('content_audio_view');
                Route::get('/{uuid}/make-favourite', [AudioContentController::class, 'makeFavourite', 'as' => 'content.audio_makeFavourite'])->name('content_audio_makeFavourite');
                Route::post('/{uuid}/request-access', [AudioContentController::class, 'requestAccess', 'as' => 'content.audio_requestAccess'])->name('content_audio_requestAccess');
                Route::post('/{uuid}/report', [AudioContentController::class, 'report', 'as' => 'content.audio_report'])->name('content_audio_report');
                Route::post('/search-query', [AudioContentController::class, 'search_query', 'as' => 'content.audio_search_query'])->name('content_audio_search_query');
                Route::get('/file/{uuid}', [AudioContentController::class, 'audioFile', 'as' => 'audio.audioFile'])->name('content_audio_file');
            });
    
            Route::prefix('/video')->group(function () {
                Route::get('/', [VideoContentController::class, 'index', 'as' => 'content.video'])->name('content_video');
                Route::get('/{uuid}', [VideoContentController::class, 'view', 'as' => 'content.video_view'])->name('content_video_view');
                Route::get('/{uuid}/make-favourite', [VideoContentController::class, 'makeFavourite', 'as' => 'content.video_makeFavourite'])->name('content_video_makeFavourite');
                Route::post('/{uuid}/request-access', [VideoContentController::class, 'requestAccess', 'as' => 'content.video_requestAccess'])->name('content_video_requestAccess');
                Route::post('/{uuid}/report', [VideoContentController::class, 'report', 'as' => 'content.video_report'])->name('content_video_report');
                Route::post('/search-query', [VideoContentController::class, 'search_query', 'as' => 'content.video_search_query'])->name('content_video_search_query');
            });
    
        });

    });

    
    Route::get('/sign-out', [UserLogoutController::class, 'get', 'as' => 'logout.get'])->name('signout');
});