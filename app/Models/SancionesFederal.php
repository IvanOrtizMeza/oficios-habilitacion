<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SancionesFederal extends Model
{
    //
    use HasFactory;
    protected $table = 'sancionados';
    protected $fillable = [
        'tipo_origen',  
        'dependencia',
        'rfc',
        'homo',
        'apellido_paterno',
        'apellido_materno',
        'nombre',
        'autoridad_sancionadora',
        'puesto',
        'periodo',
        'fecha_resolucion',
        'fecha_notificacion',
        'fecha_inicio',
        'fecha_fin',
        
    ];
}


//'excelFile' => 'required|file|mimes:xls,xlsx|max:5120',