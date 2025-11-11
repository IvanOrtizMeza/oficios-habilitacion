<?php

namespace App\Livewire;

use Livewire\Component;

class HeaderDashboard extends Component
{
    
      public $activeTab = 'solicitudes';
      public function selectTab($option){
        if($option == 'solicitudes'){
            
        }

      }
    public function render()
    {
        return view('livewire.header-dashboard');
    }
}
