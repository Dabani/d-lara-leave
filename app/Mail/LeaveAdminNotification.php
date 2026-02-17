<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data) {}

    public function build()
    {
        return $this->subject('Leave Request Update from Admin')
                    ->markdown('emails.leave.admin-notification')
                    ->with($this->data);
    }
}
