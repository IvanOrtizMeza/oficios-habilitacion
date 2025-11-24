<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;

class ResultadoSolicitudMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $nombre;
    public $estado;
    public $pdfPath;
    public function __construct($solicitud,$pdfPath)
    {
        $this->nombre = $solicitud->nombre;
        $this->estado = $solicitud->estado == 1 ? 'Aprobado' : ($solicitud->estado == 2 ? 'Rechazado' : '');
        $this->pdfPath = $pdfPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Resultado de tu solicitud de no habilitacion',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.resultado-solicitud',
            with: [
            'nombre' => $this->nombre,
            'estado' => $this->estado,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath(Storage::disk('local')->path($this->pdfPath))
                ->as('Oficio.pdf')
                ->withMime('application/pdf'),
        ];
    }
}