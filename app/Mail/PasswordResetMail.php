<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PasswordResetMail extends Mailable
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject(__('emails.password_reset_subject'))
                    ->view('emails.password_reset')
                    ->with([
                        'token' => $this->token,
                        'url' => url('password/reset/' . urlencode($this->token)),
                    ]);
    }
}
