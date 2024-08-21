<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotitificationAlertMail extends Mailable
{
    use Queueable, SerializesModels;
    public $mailData;
    public $subject;
    public $category;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData, $subject, $category)
    {
        //
        $this->mailData = $mailData;
        $this->subject = $subject;
        $this->category = $category;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->category == 'incident') {
            return $this->subject($this->subject)->view('notification.emailIncident');
        } else if ($this->category == 'deployment') {
            return $this->subject($this->subject)->view('notification.emailDeployment');
        }
    }
}
