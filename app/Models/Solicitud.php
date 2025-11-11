<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Solicitud extends Model
{
    //
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'nombre',
        'rfc',
        'curp',
        'estado',
        'correo_enviado',
        'fecha_envio_correo',
        'email',
    ];
    public function statusSolicitud()
    {
         return $this->belongsTo(StatusSolicitud::class, 'estado', 'id_estado');
    }
}
