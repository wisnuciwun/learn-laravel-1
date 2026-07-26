<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportReply extends Mailable
{
    use Queueable, SerializesModels;

    public $messageBody;

    public function __construct($messageBody)
    {
        $this->messageBody = $messageBody;
    }

    public function build()
    {
        return $this->subject('Support Response')
            ->view('emails.support_reply');
    }
}

