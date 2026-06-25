<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationCancellation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Registration $registration)
    {
        $registration->load(['activity', 'caiSection']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cancellazione iscrizione – Respira la Montagna – 5 luglio 2026',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-cancellation',
        );
    }
}
