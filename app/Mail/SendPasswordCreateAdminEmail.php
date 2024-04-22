<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendPasswordCreateAdminEmail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    private $user;
    private $admin;
    private $loginUrl;

    public function __construct($data_send)
    {
        $this->user = $data_send['user'];
        $this->admin = $data_send['admin'];
        $this->loginUrl = $data_send['url'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $textTitle = 'Creating a new user administrator';
        if ($this->user['is_change_password'] == 1) {
            $textTitle = 'Change a new email administrator';
        }
        return new Envelope(
            subject: __($textTitle),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $view  = 'emails.send-mail-password-create-admin';
        if ($this->user['is_change_password'] == 1) {
            $view  = 'emails.send-mail-change-email-admin';
        }
        return new Content(
            view: $view,
            with: [
                'admin_name' => $this->admin['full_name'],
                'name' => $this->user['name'],
                'email' => $this->user['email'],
                'created_at' => Carbon::parse($this->user['created_at'])->format('Y/m/d H:i:s'),
                'password' => $this->user['real_password'],
                'is_change_password' => $this->user['is_change_password'],
                'url' => $this->loginUrl,
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
