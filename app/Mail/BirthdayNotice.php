<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BirthdayNotice extends Mailable
{
    use Queueable, SerializesModels;

    /**
    * Create a new message instance.
    *
    * @return void
    */
    private $child;
    private $child_name_for_subject;
    private $expiration_date;

    public function __construct($child, $child_name_for_subject, $expiration_date)
    {
        $this->child = $child;
        $this->child_name_for_subject = $child_name_for_subject;
        $this->expiration_date = $expiration_date;
    }

    /**
    * Build the message.
    *
    * @return $this
    */
    public function build()
    {
        return $this->subject('Celebrate ' . $this->child_name_for_subject . ' birthday with Scribblr!')
        //FIXME email of user
        ->from("info@scribblr.be")
        ->view('emails.birthday-notice', [
            'child' => $child,
            'child_name_for_subject' => $child_name_for_subject,
            'expiration_date' => $today->addDays(7)
        ]);
    }
}
