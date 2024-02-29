<?php

namespace App\Mail\Customer;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        $store_name = !empty($this->details) ? $this->details['store_details'][0]['store_name'] : "";
        $subject = 'Welcome to '.$store_name;
        return $this->subject($subject)->view('customer.emails.registration');
    }
}
