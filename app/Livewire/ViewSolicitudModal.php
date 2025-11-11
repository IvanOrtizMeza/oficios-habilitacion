<?php

namespace App\Livewire;

use App\Models\Oficio;
use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Livewire\Forms\OficiosForm;


class ViewSolicitudModal extends Component
{
   public OficiosForm $form;
   public $id;
   public string $modalTitle = '';
    #[On("showModal")]
    public function showModal(int $id)
    {
        $this->id = $id;
        $oficio = Oficio::select('nombre','descripcion','tipo')->find($this->id);
         $this->modalTitle = $oficio->nombre;
        $this->form->fill($oficio);
        Flux::modal('edit-profile')->show();
    }
    public function cerrar(){
        Flux::modal('edit-profile')->close();
    }
    public function firmar(){
        Flux::modal('edit-profile')->close();
        
    }
    public function render()
    {
        return view('livewire.view-solicitud-modal');
    }
}
