<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $message;

    public function __construct($title, $message)
    {
        $this->title = $title;
        $this->message = $message;
    }

    public function build()
    {
        return $this->subject($this->title)
                    ->view('emails.notification')
                    ->with([
                        'title' => $this->title,
                        'message' => $this->message,
                    ]);
    }
}
