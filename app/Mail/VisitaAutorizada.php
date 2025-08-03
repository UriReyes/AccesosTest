<?php
namespace App\Mail;

namespace App\Mail;

use App\Models\Visita;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisitaAutorizada extends Mailable
{
    use Queueable, SerializesModels;

    public string $qrPath;

    public function __construct(public Visita $visita, string $qrPath)
    {
        $this->qrPath = $qrPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Tu visita ha sido autorizada');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.visitas.autorizada');
    }
}
