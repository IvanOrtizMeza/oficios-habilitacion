<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SolicitudProcesada;
use App\Models\Solicitud;
class TableSolicitudesEmitidas extends Component
{
    public $perPage = 5;
    public $search = '';
    public function getSolicitudesProperty(){
         return Solicitud::query()
          
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('rfc', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

    }
    public function render()
    {
         return view('livewire.table-solicitudes-emitidas', [
            'solicitudes' => $this->solicitudes, // llama automáticamente al getter
        ]);
       
    }
}