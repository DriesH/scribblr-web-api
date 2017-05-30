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

    public function __construct($child)
    {
        $this->child = $child;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->subject('Celebrate ' . ((substr($this->child->full_name, -1) != 's') ? $this->child->full_name . "'s" : $this->child->full_name . "'") . ' birthday with Scribblr!')
        //FIXME email of user
        ->from("info@scribblr.be")
        ->send('emails.birthday-notice', [
            'child' => $this->child
        ]);
    }
}
