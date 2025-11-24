<?php

namespace App\Livewire\Actions;
use App\Mail\ResultadoSolicitudMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEmail
{
    /**
     * Generate the PDF for the given solicitud.
     */
    public function handle($solicitud,$pdfPath,$emailDestino = null)
    {
        //pdfPath => 'oficios/' . oficio_{$solicitud->id}.pdf;
        $email = $emailDestino ?? $solicitud->email;
       
        try {
            Mail::to($email)
                ->send(new ResultadoSolicitudMail($solicitud, $pdfPath));

            $sent = true; // todo salió bien

        } catch (\Throwable $e) {
            Log::error("Error enviando correo: " . $e->getMessage());
            $sent = false;
        }

        return $sent;
    }
}