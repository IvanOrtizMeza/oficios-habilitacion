<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;


//solo usuarios no autenticados
Route::middleware('guest')->group(function () {
     Route::get('login', Login::class)->name('login');
    //si un usuario ya esta autenticado y quiere ingresar a estas rutas laravel lo manda a dashboard
   //            url      //vista o componente 
   
     Route::get('register', Register::class)->name('register');

    Volt::route('forgot-password', 'auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'auth.reset-password')
        ->name('password.reset');

});

//este bloque de route requiere usuarios autenticados o logeados
Route::middleware('auth')->group(function () {
    //           url             vista o componente
    Volt::route('verify-email', 'auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', 
        VerifyEmailController::class)//esta ruta apunta a un controlador invocable
        //con un metodo __invoke

        //signed asegura que la URL tenga una firma digital válida
        //Laravel genera enlaces firmados usando URL::signedRoute() o URL::temporarySignedRoute()
        //En el caso del email de verificación, el framework ya genera el link firmado automaticamente
        //signature = un hash que Laravel calcula con la URL + tu APP_KEY
        //Cuando llega la petición, el middleware signed recalcula el hash → si no coincide, rechaza la petición.
        //Previene que alguien manipule la URL cambiando el id o hash.

        ->middleware(['signed', 'throttle:6,1'])//limitador de peticiones max 6 intentos en 1 min
        ->name('verification.verify');//nombre de la ruta
    //           url                vista o componente
    Volt::route('confirm-password', 'auth.confirm-password')
        ->name('password.confirm');//nombre de la ruta
});

//ruta que manda llamar una clase Logout
Route::post('logout', App\Livewire\Actions\Logout::class)
    ->name('logout');