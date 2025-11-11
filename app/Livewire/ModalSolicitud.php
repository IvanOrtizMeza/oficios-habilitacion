<?php

namespace App\Livewire;

use Livewire\Component;

class ModalSolicitud extends Component
{

    protected $listeners = ['modal-solicitud' => 'cargarSolicitud'];
    public function cargarSolicitud($id)
    {
       dd("Cargar solicitud con ID: " . $id);
    }
    public function render()
    {
        return view('livewire.modal-solicitud');
    }
}
