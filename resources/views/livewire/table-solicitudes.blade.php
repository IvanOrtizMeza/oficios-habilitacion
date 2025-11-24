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
        <flux:input type="text" placeholder="Buscar Solicitud..." wire:model.live="search" />

        <flux:date-picker placeholder="Selecciona una fecha">

        </flux:date-picker>

        <flux:date-picker mode="range" min-range="3" placeholder="Selecciona un rango de fecha"
            wire:model="rangoDate" />
        <div class="h-full flex items-stretch">
            <flux:radio.group variant="buttons" class="w-full *:flex-1" wire:model.live="estadoFiltro">
                <flux:radio
                    class="data-[checked]:border-green-600 data-[checked]:text-green-700  data-[checked]:[&_[data-slot=icon]]:text-green-700"
                    icon="check" checked value="1">Habilitados
                </flux:radio>
                <flux:radio
                    class="data-[checked]:border-red-600  data-[checked]:text-red-700 data-[checked]:[&_[data-slot=icon]]:text-red-700"
                    icon="x-mark" value="2">No habilitados
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
                        <flux:input label="Nombre" :value="$solicitud->nombre ?? 'S/D'" readonly />
                    </flux:field>
                    <flux:field>
                        <flux:input label="RFC" :value="$solicitud->rfc ?? 'S/D'" readonly />
                    </flux:field>
                    <flux:field>
                        <flux:input label="Curp" :value="$solicitud->curp ?? 'S/D'" readonly />
                    </flux:field>
                    @if($solicitud->statusSolicitud->nombre === 'Habilitado')
                        <flux:field class="text-green-700">
                            <flux:input label="Estatus" :value="$solicitud->statusSolicitud->nombre ?? 'N/A'" readonly
                                style="color:green" />
                        </flux:field>
                    @elseif($solicitud->statusSolicitud->nombre === 'Rechazado')
                        <flux:field class="text-red-700">
                            <flux:input label="Estatus" :value="$solicitud->statusSolicitud->nombre ?? 'N/A'" readonly
                                style="color:red" />
                        </flux:field>
                    @else
                        <flux:field class="text-red-700">
                            <flux:input label="Estatus" :value="$solicitud->statusSolicitud->nombre ?? 'N/A'" readonly
                                style="color:blue" />
                        </flux:field>
                    @endif
                    @if($solicitud->estado == 0)
                        <flux:field class="md:col-span-2">
                            <flux:input label="Correo" :value="$solicitud->email ?? 'S/D'" readonly />
                        </flux:field>
                    @endif
                    @if($solicitud->estado !== 0)
                        <div class="col-span-full">
                            <label style="color: black;font-size: 15px;" class="block mb-1">Correo</label>
                            <div x-data="{ readonly: @entangle('readonly') }" class="flex w-full space-x-2">
                                <flux:input wire:model.defer="email" :readonly="$readonly" class="flex-1" x-ref="emailInput"
                                    x-effect="
                                            if (!readonly) {
                                                $nextTick(() =>  $refs.emailInput.focus());
                                            }" />
                                <flux:button wire:click="accionCorreo"
                                    icon="{{ $readonly ? 'pencil-square' : 'paper-airplane' }}">
                                    {{ $readonly ? 'Renviar' : 'Renviar' }}
                                </flux:button>
                            </div>
                        </div>
                   
                    
                        <flux:field>
                            <flux:input label="Email enviado"
                                :value="$solicitud->correo_enviado == 1 ? 'Enviado' : 'No enviado'" readonly />
                        </flux:field>
                        <flux:field>
                            <flux:input label="Fecha de envio" :value="$solicitud->fecha_envio_correo_formateada ?? 'SIN FECHA'"
                                readonly />
                        </flux:field>
                    @endif
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
    <flux:modal wire:model.self="showConfirmModalPDF" class="md:w-4/5 lg:w-3/4 p-6 rounded-2xl shadow-xl bg-white">
        <div class="space-y-6">
            <div class="flex items-center space-x-4 border-b border-gray-200 pb-3">
                <flux:heading size="md" class="text-gray-900 font-semibold">OFICIO CORRESPONDIENTE A ESTA SOLICITUD</flux:heading>
            </div>
            <div>

                @if($pathPDF)
                    <iframe src="{{ $pathPDF}}" class="w-full h-[93vh]" frameborder="0">
                    </iframe>
                @else
                    <p class="text-red-500">No se encontró el PDF.</p>
                @endif

            </div>
            <div class="flex justify-end space-x-3 mt-4 border-t border-gray-200">
                <flux:button variant="danger" wire:click="$set('showConfirmModalPDF', false)"
                    class="px-6 py-2 rounded-lg mt-3">
                    Cerrar
                </flux:button>
                 <flux:button variant="primary" wire:click="descargarPDF"
                    class="px-6 py-2 rounded-lg mt-3">
                    Descargar
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>