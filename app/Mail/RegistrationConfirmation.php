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

    public string $caiLogoBase64;
    public string $msLogoBase64;

    public function __construct(public Registration $registration)
    {
        $registration->load(['minors', 'activity', 'caiSection']);

        $this->caiLogoBase64 = base64_encode(file_get_contents(public_path('images/cai-logo.png')));
        $this->msLogoBase64  = base64_encode(file_get_contents(public_path('images/ms-logo.png')));
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
