<?php

namespace App\Listeners;

use App\Events\UserSocialRegistered;
use App\Mails\SendSocialRegisteredMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendSocialRegistrartionNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        // ...
    }

    /**
     * Handle the event.
     */
    public function handle(UserSocialRegistered $event): void
    {
        // Access the order using $event->order...
        if($event->user->email){
            Mail::to($event->user->email)->send(new SendSocialRegisteredMail($event->user));
        }
    }
}
