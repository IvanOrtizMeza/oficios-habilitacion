<?php

namespace App\View\Components\Tables;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TableFluxColumns extends Component
{
    /**
     * Create a new component instance.
     */
    public $columnas;
    public function __construct($columnas = [])
    {
        //
         $this->columnas = $columnas;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.tables.table-flux-columns');
    }
}