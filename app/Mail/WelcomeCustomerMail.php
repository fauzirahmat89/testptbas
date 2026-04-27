<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Selamat Datang di Toko Kami!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome_customer',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
