<?php

namespace App\Mails;

use App\Modules\Enquiries\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class SendAdminEnquiryReplyEmail extends Mailable
{
    use Queueable, SerializesModels;

    private Enquiry $enquiry;
    private string $subject_data;
    private string $message_data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Enquiry $enquiry, string $subject, string $message)
    {
        $this->enquiry = $enquiry;
        $this->subject_data = $subject;
        $this->message_data = $message;
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
            subject: 'Amrita Janani - '.$this->subject_data,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin_enquiry_reply',
            with: [
                'message' => $this->message_data,
                'data' => $this->enquiry,
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
