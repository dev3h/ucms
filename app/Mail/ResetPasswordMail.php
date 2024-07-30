<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class ResetPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    use Queueable;

    public string $token;
    public string $name;
    public string $email;
    public string $url;

    /**
     * Create a new message instance.
     */
    public function __construct($token, $name, $email, $url)
    {
        $this->token = $token;
        $this->name = $name;
        $this->email = $email;
        $this->url = $url;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __("Reset Password Mail"),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password-email',
            with: [
                'url' => $this->url,
                'name' => $this->name,
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
