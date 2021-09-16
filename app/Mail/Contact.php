<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Contact extends Mailable
{
    use Queueable, SerializesModels;

    protected $batch;
    protected $employee;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $text, $employee_name)
    {
        $this->name = $name;
        $this->text = $text;
        $this->employee_name = $employee_name;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->markdown('email.contact')
                    ->subject('Reference Check')
                    ->with([
                        'sender_name' => $this->name,
                        'text' => $this->text,
                        'employee_name' => $this->employee_name
                    ]);
    }
}
