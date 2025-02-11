<?php

namespace App\Listeners;

use App\Events\ContentAccessRequested;
use App\Mails\SendAdminContentAccessRequestEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendAdminContentAccessRequestNotification implements ShouldQueue
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
    public function handle(ContentAccessRequested $event): void
    {
        // Access the order using $event->order...
        Mail::to(config('services.admin_email'))->send(new SendAdminContentAccessRequestEmail($event->name, $event->email, $event->filename, $event->fileid, $event->filetype, $event->message));
    }
}
