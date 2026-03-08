<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerVideoConsultationLink extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $link;

    public function __construct($appointment, $link)
    {
        $this->appointment = $appointment;
        $this->link = $link;
    }

    public function build()
    {
        return $this->subject('Your Video Consultation Link')
            ->view('emails.customer-video-link');
    }
}
