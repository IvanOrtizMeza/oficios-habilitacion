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
        SolicitudHistorial::create([
            'id_solicitud' => $event->solicitud->id,
            'estado' => $event->solicitud->estado,
            'correo_enviado'     => 1,  
            'fecha_envio_correo' => now(),
        ]);
    }
}
