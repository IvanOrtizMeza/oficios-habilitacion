<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusSolicitud extends Model
{
    protected $table = 'status_solicitudes';
    protected $fillable = [
        'id_estado',   // 0 pendiente, 1 habilitado, 2 no habilitado
        'nombre',
    ];
      // Relación inversa (opcional pero recomendable)
    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'estado', 'id_estado');
    }
}
