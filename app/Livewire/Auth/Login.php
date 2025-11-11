<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        
      //  dd($this->all()); imprimo todo lo que mando en el formulario
        $this->validate();
        //valida el numero de intentos en el login
        $this->ensureIsNotRateLimited();
        //busco en la bd el email y la contraseña del usuario a logear 
        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }
        //si falla en el login mando voy sumando el numero de intentos
        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        // RateLimiter::tooManyAttempts($this->throttleKey(), 5) revisa si el usuario ya superó 5 intentos fallidos de autenticación.
        //$this->throttleKey() genera una "llave única" (normalmente combinando IP + email) para diferenciar usuarios.
        //Si NO se ha superado el límite de intentos, simplemente hace return; y deja continuar el login.
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }
        //Si el usuario superó el límite, se lanza el evento Lockout.
        event(new Lockout(request()));
        //Calcula el tiempo de espera restante para volver a logearte
        $seconds = RateLimiter::availableIn($this->throttleKey());
        //Se lanza un error de validación asociado al campo email.
        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        //convierto el email en minuscula y le concateno la ip del cliente
        //"juan.perez@gmail.com|192.168.0.10" para saber si el usuario ya alcanzo el limite de intentos
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}