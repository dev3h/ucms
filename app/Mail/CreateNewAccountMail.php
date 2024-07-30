<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CreateNewAccountMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    private $user;
    private $admin;
    private $loginUrl;
    private $routeName;

    public function __construct($data_send)
    {
        $this->user = $data_send['user'];
        $this->admin = $data_send['admin'];
        $this->loginUrl = $data_send['url'];
        $this->routeName = $data_send['routeName'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $textTitle = __("email.create-new-account");
        if($this->routeName->is('admin.api.*')){
            $textTitle = __("email.create-new-account-admin");
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
        $view  = 'emails.send-mail-create-account';
        $data = [
            'admin_name' => $this->admin['name'],
            'name' => $this->user['name'],
            'email' => $this->user['email'],
            'created_at' => Carbon::parse($this->user['created_at'])->format('Y/m/d H:i:s'),
            'password' => $this->user['real_password'],
            'is_change_password_first' => $this->user['is_change_password_first'],
            'url' => $this->loginUrl,
        ];
        if($this->routeName->is('admin.api.*')){
            $view  = 'emails.send-mail-create-account';
        }
        return new Content(
            view: $view,
            with: $data,
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
