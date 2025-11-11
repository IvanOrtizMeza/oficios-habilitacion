<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);
        //encripto la ocntraseña (hash)
        $validated['password'] = Hash::make($validated['password']);

        //User::create($validated) → crea un nuevo registro en la tabla users usando los datos validados.
        //$user ahora es una instancia del modelo recién creado.
        //event(new Registered($user)) → dispara un evento de Laravel Registered, útil si tienes listeners como enviar un email de verificación.

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);//inicia sesion con el usuario recien creado

        //mando al usuario al dashboard
        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}
