<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class EmailResetMail extends Mailable
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject(__('emails.email_change_subject'))
                    ->view('emails.email_change')
                    ->with([
                        'token' => $this->token,
                        'url' => url('email/change/' . urlencode($this->token)),
                    ]);
    }
}
