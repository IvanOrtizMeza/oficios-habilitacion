<?php

namespace App\Livewire\Forms;
use Livewire\Attributes\Validate;


use Livewire\Form;

class OficiosForm extends Form
{
     //validacion de datos
    #[Validate('required|string|min:3|max:100')]
    public string $nombre = '';

    #[Validate('required|string|min:3|max:100')]
    public string $descripcion = '';
    
    #[Validate('required|string|min:3|max:100')]
    public string $tipo = '';
}