<?php

namespace App\Listeners;

use App\Events\AdminEnquiryReplied;
use App\Mails\SendAdminEnquiryReplyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendAdminEnquiryReplyNotification implements ShouldQueue
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
    public function handle(AdminEnquiryReplied $event): void
    {
        // Access the order using $event->order...
        if($event->enquiry->email){
            Mail::to($event->enquiry->email)->send(new SendAdminEnquiryReplyEmail($event->enquiry, $event->subject, $event->message));
        }
    }
}
