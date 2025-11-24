<?php

namespace App\Livewire\Actions;

use App\Models\SancionesFederal;
use Carbon\Carbon;

class GetStatus
{

    public function handle($solicitud)
    {
        /*$tieneSancionVigente = Sancionado::where('rfc', $rfc)
            ->whereDate('fecha_fin', '>=', today())
            ->exists();

        if ($tieneSancionVigente) {
            // NO PASA
        } else {
            // SÍ PASA
        }*/
        $hoy = Carbon::today();
        $existeVigente = SancionesFederal::where('rfc', $solicitud->rfc)
        ->whereDate('fecha_fin', '>=', $hoy)
        ->exists();
        return $existeVigente ? 2 : 1;
        /*$existe = SancionesFederal::where('rfc', $solicitud->rfc)->first();
        return $existe ? ($existe->fecha_fin >= $hoy ? 2 : 1) : 1;*/
    }
}
