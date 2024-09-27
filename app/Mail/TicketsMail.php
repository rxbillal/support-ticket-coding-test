<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @param $view
     * @param $subject
     * @param  array  $data
     */
    public function __construct($view, $subject, $data = [])
    {
        $this->data = $data;
        $this->view = $view;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return TicketsMail
     */
    public function build()
    {
        $mail = $this->subject($this->subject)
            ->markdown($this->view)
            ->with($this->data);

        return $mail;
    }
}
