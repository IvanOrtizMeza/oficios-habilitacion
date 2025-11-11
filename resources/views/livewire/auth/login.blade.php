
<div class="flex flex-col gap-6">
    <!--hago uso del componente auth-header y le paso los parametros title y description -->

    <!-- Session Status -->
     <!-- Muestra mensajes de estado de sesión, como "Has cerrado sesión" o "Tu contraseña ha sido cambiada" -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <!-- Formulario de login que llama al metodo login -->
    <form  wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <!--  wire:model=email  Conecta el campo del formulario con la propiedad pública $email de tu componente Livewire.-->
        <flux:input
            wire:model="email"
            :label="__('Correo electrónico')"
            type="text"
            required
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <!-- viewable   agrega un icono de ojo para mostrar/ocultar la contraseña -->
        <div class="relative">
            <flux:input
                wire:model="password"
                :label="__('Contraseña')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Contraseña')"
                viewable
            />
            <!--esto es por si olvidaste contraseña-->
            <!--revisa si existe la ruta con ese nombre (password.request) en routes/web.php.-->
            @if (Route::has('password.request'))
                <!--si la ruta existe muestra el enlace de recuperacion de contraseña-->
                <!-- Posiciona el enlace en la esquina superior derecha del contenedor padre -->

               
            @endif
        </div>

        <!-- Remember Me -->
        <flux:checkbox wire:model="remember" :label="__('Recordar inicio de sesión')" />

        <div class="flex items-center justify-end">
            <flux:button 
                type="submit" 
                class="w-full text-white! bg-[#611232]! hover:bg-[#71785B]!" 
                data-test="login-button"
               
            >
                {{ __('INGRESAR') }}
            </flux:button>
        </div>
         <!--enlace de recuperacion de contraseña mediante flux:link    ruta de navegacion-->
        <flux:link class="text-sm text-center" :href="route('password.request')" wire:navigate>
            {{ __('¿Olvidaste tu contraseña?') }}
        </flux:link>
    </form>
    <!--si la ruta register existe en routes/web.php muestra el enlace de registro-->
    <!--esto es por si no tienes cuenta-->
   {{--   @if (Route::has('register'))
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('No tienes una cuenta?') }}</span>
            <!--          ruta de navegacion     SPA navegacion-->
            <flux:link :href="route('register')" wire:navigate>{{ __('Registrate') }}</flux:link>
        </div>
    @endif
    --}}
</div>
