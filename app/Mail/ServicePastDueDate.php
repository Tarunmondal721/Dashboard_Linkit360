<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ServicePastDueDate extends Mailable
{
    use Queueable, SerializesModels;
    public $mailData;
    public $name;
    public $serviceName;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData, $name, $serviceName)
    {
        //
        $this->mailData = $mailData;
        $this->name = $name;
        $this->serviceName = $serviceName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->subject('Service Past Due Date')->view('email.service_past_due_date');
    }
}
