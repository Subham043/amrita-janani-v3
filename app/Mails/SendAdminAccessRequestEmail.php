<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAdminAccessRequestEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $detail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($detail)
    {
        $this->detail = $detail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Amrita Janani - Access Request')->view('emails.admin_access_request')->with([
            'detail' => $this->detail,
        ]);
    }
}
