<?php

namespace App\Events;

use App\Modules\Enquiries\Models\Enquiry;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminEnquiryReplied
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Enquiry $enquiry,
        public string $subject,
        public string $message,
    ) {
    }
}
