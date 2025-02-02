<?php

namespace App\Mails;

use App\Modules\Enquiries\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class SendUserThankYouEmail extends Mailable
{
    use Queueable, SerializesModels;

    private Enquiry $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Enquiry $data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address('no-reply@amrita-janani.org', 'Amrita Janani'),
            ],
            subject: 'Amrita Janani - Enquiry Received',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.thank_you',
            with: [
                'name' => $this->data->name,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

}
