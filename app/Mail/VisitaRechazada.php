<?php
namespace App\Mail;

use App\Models\Visita;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisitaRechazada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Visita $visita,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu visita ha sido rechazada',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.visitas.rechazada',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
