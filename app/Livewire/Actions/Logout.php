<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        //Cierra la sesión del usuario autenticado bajo el guard 'web'.
        //Quita la información de usuario de la sesión y cualquier “remember me” token.
        //Después de esto, Auth::user() devuelve null.
        Auth::guard('web')->logout();

    //Borra todos los datos de la sesión (incluyendo cualquier variable almacenada).
    //Evita que un atacante pueda reutilizar la sesión anterior.
        Session::invalidate();

        //regenera el token de la sesion
        Session::regenerateToken();


        //retorna al usuario al inicio login
        return redirect('/');
    }
}
