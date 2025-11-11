<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SolicitudProcesada;
use Livewire\WithPagination;
use App\Models\Solicitud;
class TableSolicitudesRechazadas extends Component
{
    use WithPagination;

    public $perPage = 5;
    public $search = '';
    public $selected = [];//guarda los ids de los registros que el usuario a seleccionado
    public $selectAll = false;
    //hook automatico que escucha el cambio de selectAll
    public function updatedSelectAll($value)
    {
       
        //Si $selectAll es true:
        //Se llenan todos los IDs de $solicitudes en $selected, marcando todas las filas como seleccionadas.
        if ($value) {
            //aqui el array solicitudes lo convierto en una coleccion para usar pluck
            //pluck extrae solo los ids y toArray lo convierte de nuevo en array
            //en otras palabras convierto solicitudes en una coleccion y extraigo solo los ids con pluck y convierto esto en un nuevo array de ids
            $this->selected = $this->solicitudes->getCollection()->pluck('id')->toArray();
           
        } else {//si es falso vacio los selected
            $this->selected = [];
        }
    }

    public function getSolicitudesProperty()
    {
        return Solicitud::query()
            ->where('estado', 2)
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
       // return view('livewire.table-solicitudes-rechazadas');

        return view('livewire.table-solicitudes-rechazadas', [
            'solicitudes' => $this->solicitudes, // llama automáticamente al getter
        ]);
    }
}