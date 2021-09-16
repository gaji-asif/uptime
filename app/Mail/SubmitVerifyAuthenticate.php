<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Employee;

class SubmitVerifyAuthenticate extends Mailable
{
    use Queueable, SerializesModels;

    protected $verifycode;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($verifycode)
    {
        $this->verifycode = $verifycode;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->verifycode . ' is your UptimeProfile verification code.')
            ->markdown('email.submissions.authenticate')
            ->with('verifycode', $this->verifycode);
    }
}
