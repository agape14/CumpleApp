<?php

namespace App\Mail;

use App\Models\Familiar;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BirthdayReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Crea una nueva instancia del mensaje.
     */
    public function __construct(
        public Familiar $familiar
    ) {}

    /**
     * Obtiene el envelope del mensaje.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🎂 ¡Hoy es el cumpleaños de {$this->familiar->nombre}!",
        );
    }

    /**
     * Obtiene la definición del contenido del mensaje.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.birthday-reminder',
        );
    }

    /**
     * Obtiene los archivos adjuntos del mensaje.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

