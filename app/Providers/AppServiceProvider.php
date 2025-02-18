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
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\CpuLoadHealthCheck\CpuLoadCheck;
use Spatie\Health\Checks\Checks\DatabaseSizeCheck;
use Spatie\SecurityAdvisoriesHealthCheck\SecurityAdvisoriesCheck;

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

        Health::checks([
            DebugModeCheck::new(),
            DatabaseCheck::new(),
            OptimizedAppCheck::new(),
            UsedDiskSpaceCheck::new()
                ->warnWhenUsedSpaceIsAbovePercentage(90)
                ->failWhenUsedSpaceIsAbovePercentage(95),
            SecurityAdvisoriesCheck::new(),
            CacheCheck::new(),
            DatabaseSizeCheck::new()
                ->failWhenSizeAboveGb(errorThresholdGb: 5.0),
            EnvironmentCheck::new(),
            CpuLoadCheck::new()
                ->failWhenLoadIsHigherInTheLast5Minutes(2.0)
                ->failWhenLoadIsHigherInTheLast15Minutes(1.5),
        ]);

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
