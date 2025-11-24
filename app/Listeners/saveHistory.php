<?php

namespace App\Listeners;

use App\Events\ProcessRequest;
use App\Models\SolicitudHistorial;
use Illuminate\Support\Facades\DB;

class saveHistory
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProcessRequest $event): void
    {
        //$solicitud = $event->solicitud;

        // Verifica que la solicitud realmente esté marcada como enviada
        //if (!$solicitud->correo_enviado || !$solicitud->fecha_envio_correo) {
            // Evita guardar historiales corruptos
          //  return;
        //}
        //antes de hacer este historial y poner el correo enviado a 1 revisar que en solicitudes se alla cambiado el dato tambien
        SolicitudHistorial::create([
            'id_solicitud' => $event->solicitud->id,
            'estado' => $event->solicitud->estado,
            'correo_enviado'     => $event->solicitud->correo_enviado,
            'fecha_envio_correo' => $event->solicitud->fecha_envio_correo,
        ]);
    }
}
