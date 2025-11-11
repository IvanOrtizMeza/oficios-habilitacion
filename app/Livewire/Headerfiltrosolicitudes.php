<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Enums\DocumentStatus;
class Headerfiltrosolicitudes extends Component
{

    #[Url]
    public $tabActiva = DocumentStatus::Solicitudes->name;
    public function render()
    {
        return view('livewire.headerfiltrosolicitudes');
    }
}
