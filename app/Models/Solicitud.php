<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

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
        'pdf_path',
        'fecha_creacion_pdf',
    ];
    public function statusSolicitud()
    {
        //la relacion es de estado(solicitudes) con id_estado(status_solicitudes) para acceder al nombre del estado
        return $this->belongsTo(StatusSolicitud::class, 'estado', 'id_estado');
    }

    public function getOficioUrlAttribute()
    {
        return Storage::temporaryUrl($this->pdf_path, now()->addMinutes(15));
    }
    public function getFechaEnvioCorreoFormateadaAttribute()
    {
        if (!$this->fecha_envio_correo) {
            return null;
        }

        return Carbon::parse($this->fecha_envio_correo)
            ->format('d-m-Y \a \l\a\s H:i \h\o\r\a\s');
    }
    public function getFolioDocumentoAttribute()
    {
        $prefijo = 'S.C./D.G.R/';
        // usar created_at->year para que el folio muestre el año de la solicitud si lo deseas:
        $anio = $this->created_at ? $this->created_at->year : date('Y');

        return sprintf('%s%06d/%s', $prefijo, $this->id, $anio);
    }
}
