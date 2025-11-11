<x-layouts.app :title="__('Dashboard')">

   {{--  <livewire:header-dashboard />--}}
    
    {{--  <livewire:solicitudes />--}}
    {{--  <livewire:headerfiltrosolicitudes/>--}}
    {{--  <div class="relative mb-6 w-full">
      <flux:heading size="xl" level="1">{{ __('Solicitudes') }}</flux:heading>
      <flux:subheading size="lg" class="mb-6">{{ __('Manage your profile and account settings') }}</flux:subheading>
      <flux:separator variant="subtle" />
    </div>--}}

    <x-content>
      <x-slot:header>
        <div>
          <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">
              Módulo de Solicitudes
          </h1>
          <p class="text-gray-500 dark:text-gray-400 text-sm">
              Gestión y seguimiento de las solicitudes activas del sistema
          </p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center gap-3">
          <flux:button color="primary">
              Nueva Solicitud
          </flux:button>
          <flux:button variant="outline">
              Exportar
          </flux:button>
        </div>
      </x-slot>
      <x-slot:titleCont>
            Contenido del módulo   
      </x-slot>
      <x-slot:body>
                  Contenido
      </x-slot>
    </x-content>
</x-layouts.app>
