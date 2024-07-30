<?php

namespace App\Jobs;

use App\Mail\ResetPasswordMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SendResetPasswordMail implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $token;
    protected $route;

    /**
     * Create a new job instance.
     */

    public function __construct($user, $token, $route)
    {
        $this->user = $user;
        $this->token = $token;
        $this->route = $route;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $link_expire_time = 1;
        $url = URL::temporarySignedRoute($this->route, now()->addDays($link_expire_time), [
            'token' => $this->token,
            'email' => $this->user->email,
        ]);
        Mail::to($this->user)->send(new ResetPasswordMail($this->token, $this->user->name, $this->user->email, $url));
    }
}
