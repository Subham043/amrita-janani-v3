<?php

namespace App\Listeners;

use App\Events\EnquirySubmitted;
use App\Mails\SendAdminEnquiryEmail;
use App\Mails\SendUserThankYouEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendEnquirySubmittedNotification implements ShouldQueue
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
    public function handle(EnquirySubmitted $event): void
    {
        // Access the order using $event->order...
        if($event->enquiry->email){
            Mail::to($event->enquiry->email)->send(new SendUserThankYouEmail($event->enquiry));
        }
        Mail::to(config('services.admin_email'))->send(new SendAdminEnquiryEmail($event->enquiry));
    }
}
