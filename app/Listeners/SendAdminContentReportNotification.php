<?php

namespace App\Listeners;

use App\Events\ContentReported;
use App\Mails\SendAdminContentReportEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendAdminContentReportNotification implements ShouldQueue
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
    public function handle(ContentReported $event): void
    {
        // Access the order using $event->order...
        Mail::to(config('services.admin_email'))->send(new SendAdminContentReportEmail($event->name, $event->email, $event->filename, $event->fileid, $event->filetype, $event->message));
    }
}
