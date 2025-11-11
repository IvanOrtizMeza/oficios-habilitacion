<?php

namespace App\Livewire\Actions;
use App\Models\SancionesFederal;
use Carbon\Carbon;
class GetStatus
{
   
    public function handle($solicitud)
    {
        $existe = SancionesFederal::where('rfc', $solicitud->rfc)->first();
        $hoy = Carbon::today();
        return $existe ? ($existe->fecha_fin >= $hoy ? 2 : 1) : 1;
    }
}