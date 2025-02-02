<?php

namespace App\Providers;

use App\Events\EnquirySubmitted;
use App\Events\UserSocialRegistered;
use App\Listeners\SendEnquirySubmittedNotification;
use App\Listeners\SendSocialRegistrartionNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
        //

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
    }
}
