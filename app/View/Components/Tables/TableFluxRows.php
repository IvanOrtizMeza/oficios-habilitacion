<?php

namespace App\View\Components\Tables;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TableFluxRows extends Component
{
    
    public $datos;

    public function __construct($datos = [])
    {
        $this->datos = $datos;
           
    }

    public function render(): View|Closure|string
    {
        return view('components.tables.table-flux-rows');
    }
}