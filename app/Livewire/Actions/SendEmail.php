<?php

namespace App\Livewire\Actions;
use App\Mail\ResultadoSolicitudMail;
use Illuminate\Support\Facades\Mail;
class SendEmail
{
    /**
     * Generate the PDF for the given solicitud.
     */
    public function handle($solicitud,$pdfPath)
    {
        Mail::to($solicitud->email)
            ->send(new ResultadoSolicitudMail($solicitud,$pdfPath));
        if (file_exists($pdfPath)) {
            unlink($pdfPath);
        }
    }
}