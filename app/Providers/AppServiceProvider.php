<?php

namespace App\Providers;

use App\Events\AdminEnquiryReplied;
use App\Events\ContentAccessRequested;
use App\Events\ContentReported;
use App\Events\EnquirySubmitted;
use App\Events\UserSocialRegistered;
use App\Listeners\SendAdminContentAccessRequestNotification;
use App\Listeners\SendAdminContentReportNotification;
use App\Listeners\SendAdminEnquiryReplyNotification;
use App\Listeners\SendEnquirySubmittedNotification;
use App\Listeners\SendSocialRegistrartionNotification;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return URL::temporarySignedRoute($user->isAdmin() ? 'reset_password' : 'password.reset', now()->addMinutes(60), ['token' => $token]);
        });

        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('google', \SocialiteProviders\Google\Provider::class);
        });

        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('facebook', \SocialiteProviders\Facebook\Provider::class);
        });

        Event::listen(
            UserSocialRegistered::class,
            SendSocialRegistrartionNotification::class,
        );

        Event::listen(
            EnquirySubmitted::class,
            SendEnquirySubmittedNotification::class,
        );

        Event::listen(
            AdminEnquiryReplied::class,
            SendAdminEnquiryReplyNotification::class,
        );

        Event::listen(
            ContentAccessRequested::class,
            SendAdminContentAccessRequestNotification::class,
        );

        Event::listen(
            ContentReported::class,
            SendAdminContentReportNotification::class,
        );

    }
}
