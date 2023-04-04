<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PDF;

class InvoiceHourlyRate extends Mailable
{
    use Queueable, SerializesModels;

    public $projectTimer;
    public $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($projectTimer, $email)
    {
        $this->projectTimer = $projectTimer;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // $pdf = PDF::loadView('emails.projects.invoices.invoices');
        return $this->subject("Prooflo | Invoice hourly rate")->view('emails.projects.invoices.invoices');
        // return $this->subject("Prooflo | Invoice hourly rate")->view('emails.projects.invoices.invoices')->attachData($pdf->output(), "invoice.pdf");
    }
}
