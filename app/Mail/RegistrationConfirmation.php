<?php

namespace App\Mail;

use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Registration $registration)
    {
        $registration->load(['minors.caiSection', 'activity', 'caiSection']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Conferma iscrizione – Respira la Montagna – 5 luglio 2026',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-confirmation',
        );
    }
}
