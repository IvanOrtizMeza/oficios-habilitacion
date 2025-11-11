<div class="p-1">
    <flux:toast />
    
    
        <flux:button wire:click="guardarSolicitudes">
            Guardar solicitudes en BD
        </flux:button>

        @if (session()->has('message'))
        <div class="mt-2 text-green-600">{{ session('message') }}
        </div>
        @endif
        <flux:button wire:click="procesar">
            Procesar Solicitudes
        </flux:button>

        @if (session()->has('message'))
        <div class="mt-2 text-green-600">{{ session('message') }}</div>
        @endif
    <!-- 🔸 Filtros y búsqueda -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2 mt-2">
        <flux:input type="text" placeholder="Buscar Solicitud..." wire:model.live="search"  />
       
        <flux:date-picker  placeholder="Selecciona una fecha">
             
        </flux:date-picker>
        
        <flux:date-picker mode="range" min-range="3" placeholder="Selecciona un rango de fecha" wire:model="rangoDate"/>
        <div class="h-full flex items-stretch">
            <flux:radio.group variant="buttons" class="w-full *:flex-1" wire:model.live="estadoFiltro">
                <flux:radio  
                    class="data-[checked]:border-green-600 data-[checked]:text-green-700  data-[checked]:[&_[data-slot=icon]]:text-green-700" 
                    icon="check" 
                    checked
                    value="1"
                >Habilitados
                </flux:radio>
                <flux:radio 
                    class="data-[checked]:border-red-600  data-[checked]:text-red-700 data-[checked]:[&_[data-slot=icon]]:text-red-700" 
                    icon="x-mark"
                    value="2"
                >No habilitados
                </flux:radio>
            </flux:radio.group>
        </div>
    </div>

    <div class="!border border-gray-300 rounded-lg shadow-sm overflow-hidden">
        <flux:table>
            <!-- 🔹 Encabezados -->
            <x-tables.table-flux-columns :columnas="['Nombre', 'RFC', 'CURP', 'Estado', 'Acciones']" />
            <!-- 🔹 body -->
            <x-tables.table-flux-rows :datos="$solicitudes" />
        </flux:table>
    </div>
    <!-- 🔸 Paginación -->
    <div class="mt-4 flex justify-start">
        {{ $solicitudes->links() }}
    </div>


    <flux:modal wire:model.self="showConfirmModal" class="md:w-2/3 lg:w-1/2 p-6 rounded-2xl shadow-xl bg-white">
        <div class="space-y-6">

            <!-- Encabezado con icono -->
            <div class="flex items-center space-x-4 border-b border-gray-200 pb-3">
            
                <flux:heading size="lg" class="text-gray-900 font-semibold">Detalle de Solicitud</flux:heading>
            </div>

            <!-- Información de la solicitud -->
            @if($solicitud)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                
                    <flux:field>
                        <flux:input label="Nombre" :value="$solicitud->nombre ?? ''" readonly />
                    </flux:field>
                    <flux:field>
                        <flux:input label="RFC" :value="$solicitud->rfc ?? ''" readonly />
                    </flux:field>
                    <flux:field>
                        <flux:input label="Curp" :value="$solicitud->curp ?? ''" readonly />
                    </flux:field>
                    @if($solicitud->statusSolicitud->nombre === 'Habilitado')
                        <flux:field class="text-green-700">
                            <flux:input label="Estatus" :value="$solicitud->statusSolicitud->nombre ?? 'N/A'" readonly style="color:green"/>
                        </flux:field>
                    @else
                        <flux:field class="text-red-700">
                            <flux:input label="Estatus" :value="$solicitud->statusSolicitud->nombre ?? 'N/A'" readonly style="color:red"/>
                        </flux:field>
                    @endif
                        
                    <div class="md:col-span-2">
                        <flux:field>
                            <flux:input label="Correo" :value="$solicitud->email ?? 'N/A'" readonly />
                        </flux:field>
                    </div>
                    <flux:field>
                        <flux:input label="Email enviado"  :value="$solicitud->correo_enviado == 1 ? 'Enviado' : 'No enviado'"  readonly />
                    </flux:field>
                     <flux:field>
                        <flux:input label="Fecha de envio" :value="$solicitud->fecha_envio_correo  ?? ''" readonly />
                    </flux:field>
                </div>
            @else
                <flux:text class="mt-2 text-gray-500">Cargando...</flux:text>
            @endif

            <!-- Botones -->
            <div class="flex justify-end space-x-3 mt-4">
                <flux:button variant="danger" wire:click="$set('showConfirmModal', false)" class="px-6 py-2 rounded-lg">
                    Cerrar
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>