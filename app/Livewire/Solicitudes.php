<?php

namespace App\Livewire;
use App\Enums\DocumentStatus;


use Livewire\Component;

class Solicitudes extends Component
{
    public $tabActiva = DocumentStatus::Solicitudes->name;
    public function mount()
    {
        // Cuando se carga el componente, por defecto selecciona "Solicitudes"
        $this->tabActiva = DocumentStatus::Solicitudes->name;
    }
    public function render()
    {
        return view('livewire.solicitudes');
    }
}