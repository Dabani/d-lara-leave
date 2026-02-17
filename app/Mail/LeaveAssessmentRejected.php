<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveAssessmentRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data) {}

    public function build()
    {
        return $this->subject('Leave Request Rejected by Assessor')
                    ->markdown('emails.leave.assessment-rejected')
                    ->with($this->data);
    }
}
