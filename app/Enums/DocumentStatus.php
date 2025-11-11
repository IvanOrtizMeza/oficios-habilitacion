<?php

namespace App\Enums;

enum DocumentStatus: int
{
    //   name    value(1,2,3)  
    case Solicitudes = 0;
    case Emitidas = 1;
    //case Pendientes = 2;
  
   /* case Recibidas = 'Recibidas';
    case Pendientes = 'Rechazado';
    case Emitidas = 'Emitidas';*/
    public function description(): string
    {
        return match($this) {
            self::Solicitudes => "Solicitudes",
            self::Emitidas => 'Emitidas',
            //self::Pendientes => 'Pendientes',
        };
    }
    
    
}