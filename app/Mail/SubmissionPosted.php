<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Batch;
use App\Builds;
use App\Employee;

class SubmissionPosted extends Mailable
{
    use Queueable, SerializesModels;

    protected $batch;
    protected $employee;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Batch $batch, Employee $employee)
    {
        $this->batch = $batch;
        $this->employee = $employee;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->markdown('email.submissions.posted')
                    ->subject($this->employee->full_name . '\'s Uptime Reference Request')
                    ->with([
                        'batch' => $this->batch,
                        'employee' => $this->employee,
                        'url' => route('verify.submission', [
                            'uuid' => $this->batch->id
                        ])
                    ]);
                
    }
}
