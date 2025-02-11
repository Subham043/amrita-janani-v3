<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class SendAdminContentAccessRequestEmail extends Mailable
{
    use Queueable, SerializesModels;

    private string $name;
    private string $email;
    private string $filename;
    private string $fileid;
    private string $filetype;
    private string $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        string $name,
        string $email,
        string $filename,
        string $fileid,
        string $filetype,
        string $message,
    )
    {
        $this->name = $name;
        $this->email = $email;
        $this->filename = $filename;
        $this->fileid = $fileid;
        $this->filetype = $filetype;
        $this->message = $message;
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
            subject: 'Amrita Janani - Access Request',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin_access_request',
            with: [
                'name' => $this->name,
                'email' => $this->email,
                'filename' => $this->filename,
                'fileid' => $this->fileid,
                'filetype' => $this->filetype,
                'message' => $this->message,
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
