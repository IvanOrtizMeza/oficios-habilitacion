<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\HeaderDashboard;
use App\Livewire\Headerfiltrosolicitudes;
use App\Livewire\ImportDB;


//ruta de inicio la que entra al welcome y la nombro home
Route::get('/', function () {
    return redirect()->route('dashboard');//regresa la vista welcome que esta en resource/views/welcome.blade.php
})->name('home'); //asigna el nombre a mi ruta (home)
// ejemplo   <a href="{{ route('home') }}">Inicio</a>



Route::view('dashboard', 'dashboard')//crea una ruta de solo vista sin controlador (el primer parametro es la url y el segundo es la vista .blade )
    ->middleware(['auth', 'verified'])//se controla el acceso a esta ruta 
    //auth : solo es accesible si el usuario esta autenticado
    //verified: solo accesible si el correo del usuario esta verificado
    ->name('dashboard');//asigno nombre a la ruta 


Route::group(['middleware' => ['auth', 'verified']], function() {
    Route::get('solicitudes',Headerfiltrosolicitudes::class)->name('solicitudes');
    Route::get('importdb',ImportDB::class)->name('importdb');
    Route::get('pdf', function () {
    return view('PDF.oficio'); 
    });
    Route::get('prueba',HeaderDashboard::class)->name('prueba');
});


    //Middleware: filtros que deciden si un usuario puede acceder a la ruta.

//🔹 auth

//Solo permite usuarios autenticados.

//Si un visitante no ha iniciado sesión, Laravel lo redirige automáticamente a la página de login.

//🔹 verified

//Solo permite usuarios con correo electrónico verificado.

//Si el usuario está autenticado pero no verificó su email, Laravel lo redirige a la página de verificación de correo.




//----------------------------------------------------------------------------------------
//esto crea un grupo de rutas protegidas con auth solo usuarios autenticados podran acceder a ellas
//es decir todo lo que esta dentro necesita usuario logeado de no ser asi retorna a login
Route::middleware(['auth'])->group(function () {

    //ruta que redirige automaticamente
    //cuando alguien entre a settings laravel lo envia a settings/profile
    Route::redirect('settings', 'settings/profile');

    //volt como sistema de rutas
    //Funciona parecido a Route::get o Route::view, 
    // pero permite una sintaxis más compacta para rutas 
    // que apuntan a componentes o controladores internos.

    //           url ruta           componente                 nombre ruta
    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
});

require __DIR__.'/auth.php'; //incluyo otro archio de rutas que se encuentra en la misma carpeta
//routes y el archivo es auth para mantener limpio este 
//en otras palabras todas las rutas de autenticacion estan en otro archivo