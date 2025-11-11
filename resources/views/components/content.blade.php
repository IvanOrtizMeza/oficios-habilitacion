<div class="flex h-full w-full flex-col gap-6  mt-5 lg:-mt-5">
    <!-- 🟦 CARD SUPERIOR (Encabezado / título del módulo) -->
    <div class="relative overflow-hidden rounded-2xl border shadow-md hover:shadow-2xl  border-gray-200 dark:border-neutral-700 bg-white/90 dark:bg-neutral-900/80 backdrop-blur-md  transition-all duration-300">
      <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between">
        {{ $header }}
        <!-- <div>
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
          </div>-->
      </div>
    </div>
    <!-- ⚪ CARD INFERIOR (Contenido principal) -->
    <div class="relative flex-1 overflow-hidden rounded-xl border border-gray-200 dark:border-neutral-700 bg-white/90 dark:bg-neutral-800/90 backdrop-blur-md shadow-sm hover:shadow-md transition-all duration-300">            
      <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
            {{ $titleCont }}
        </h2>   
        <!-- Aquí irá tu contenido dinámico -->
        <div class="text-gray-500 dark:text-gray-400">
          <div class="overflow-x-auto w-full">
            {{ $body }}
          </div>
        </div>
      </div>
    </div>
  </div>