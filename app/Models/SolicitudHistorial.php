<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudHistorial extends Model
{
    //
    protected $table = 'solicitud_historial';
    protected $fillable = [
        'id_solicitud',
        'estado',
        'correo_enviado',
        'fecha_envio_correo',
    ];
}
