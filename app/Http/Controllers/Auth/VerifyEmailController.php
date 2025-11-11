<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    //recibe el EmailVerificationRequest para validar email
    //Verifica que el ID ({id}) en la URL corresponda al usuario logueado.
    //Valida que el hash en la URL sea correcto.
    //Valida que el hash en la URL sea correcto.
    //este Request ya te da todo validado automáticamente
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        //Pregunta: ¿el usuario ya verificó su correo antes?
        //Si sí → no repite el proceso, solo lo redirige al dashboard con el query ?verified=1.
        if ($request->user()->hasVerifiedEmail()) {
            //$user = $request->user(); es como si hiciera esto con $request->user()
            //Auth::user()  contiene el usuario autenticado de la aplicacion instancia del modelo USER
            //App\Models\User {
             //   id: 1,
              //  name: "Ivan",
               // email: "ivan@test.com",
                //email_verified_at: null,
                
            //}
            //$request->user() en resumen es el usuario autenticado
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        //si el usuario no esta verificado email_verified_at con la fecha actual
        //lo verifica con fulfill y lo regresa igual al dashboard
        $request->fulfill();

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
