<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordChange extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;
    public $token;
    public $project;

    /**
     * Create a new message instance.
     *
     * @param $name
     * @param $project
     * @param $token
     */
    public function __construct($customer, $project, $token)
    {
        $this->customer = $customer;
        $this->token = $token;
        $this->project = $project;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Project | Please Login to Review')->view('emails.customer_invited');
    }
}
