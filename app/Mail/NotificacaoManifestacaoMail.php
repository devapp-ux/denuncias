<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacaoManifestacaoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $dados) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nova manifestação recebida: '.$this->dados['codigo'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notificacao-manifestacao',
            with: ['dados' => $this->dados],
        );
    }
}
