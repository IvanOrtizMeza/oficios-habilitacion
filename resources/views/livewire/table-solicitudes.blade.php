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
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-2 mt-2">
        <flux:input type="text" placeholder="Buscar Solicitud..." wire:model.live="search" />

        <flux:date-picker placeholder="Selecciona una fecha">

        </flux:date-picker>

        <flux:date-picker mode="range" min-range="3" placeholder="Selecciona un rango de fecha"
            wire:model="rangoDate" />
        {{-- <div class="h-full flex items-stretch">
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
        --}}


        <flux:select wire:model.live="filtroSelect" variant="listbox" searchable placeholder="Selecciona una opción...">
            <flux:select.option value="1">
                <div class="flex items-center gap-2">
                    <flux:icon.check-circle variant="mini" class="text-zinc-400" /> Habilitados
                </div>
            </flux:select.option>
            <flux:select.option value="2">
                <div class="flex items-center gap-2">
                    <flux:icon.x-circle variant="mini" class="text-zinc-400" /> No Habilitados
                </div>
            </flux:select.option>
            <flux:select.option value="">
                <div class="flex items-center gap-2">
                    <flux:icon.shield-check variant="mini" class="text-zinc-400" /> Todas
                </div>
            </flux:select.option>
        </flux:select>

    </div>
    <!-- 🔸 Tabla de datos -->
    <div class="!border border-gray-300 rounded-lg shadow-sm overflow-hidden mt-7">
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
                        <flux:input class="mb-4" label="Curp" :value="$solicitud->curp ?? 'S/D'" readonly />
                    </flux:field>
                    @if($solicitud->statusSolicitud->nombre === 'Habilitado')
                        <flux:field class="mb-4 text-green-700">
                            <flux:input label="Estatus" :value="$solicitud->statusSolicitud->nombre ?? 'N/A'" readonly
                                style="color:green" />
                        </flux:field>
                    @elseif($solicitud->statusSolicitud->nombre === 'Rechazado')
                        <flux:field class="mb-4 text-red-700">
                            <flux:input label="Estatus" :value="$solicitud->statusSolicitud->nombre ?? 'N/A'" readonly
                                style="color:red" />
                        </flux:field>
                    @else
                        <flux:field class="mb-4 text-blue-700">
                            <flux:input label="Estatus" :value="$solicitud->statusSolicitud->nombre ?? 'N/A'" readonly
                                style="color:blue" />
                        </flux:field>
                    @endif
                   
                    @if($solicitud->estado !== 0)
                        <div class="md:col-span-2 space-y-3">
                            <div class="flex items-center justify-between">
                                <flux:label class="text-base font-semibold">Correo</flux:label>
                                <flux:checkbox wire:model.live="correoCheck" label="Modificar Correo" />
                            </div>
                            <flux:input label="" wire:model.live="emailRenvio" :value="$solicitud->email ?? 'S/D'"
                                :readonly="!$correoCheck" class="w-full" />

                            @if($correoCheck)
                                <flux:input label="Confirmar Correo" wire:model.live="emailRenvioConfirm"
                                    placeholder="Confirme el correo" class="w-full" />
                            @endif
                        </div>



                        <flux:field class="mt-4">
                            <flux:input label="Email enviado"
                                :value="$solicitud->correo_enviado == 1 ? 'Enviado' : 'No enviado'" readonly />
                        </flux:field>
                        <flux:field class="mt-4">
                            <flux:input label="Fecha de envio" :value="$solicitud->fecha_envio_correo_formateada ?? 'SIN FECHA'"
                                readonly />
                        </flux:field>

                    @endif
                </div>
            
            


            <div class="flex justify-end space-x-3 mt-6  border-t border-gray-200">
                @if($solicitud->estado !== 0)
                <flux:button variant="primary" wire:click="reenviar" class="px-6 py-2 mt-5 rounded-lg">
                    Reenviar Correo
                </flux:button>
                @endif
                <flux:button variant="danger" wire:click="$set('showConfirmModal', false)"
                    class="px-6 py-2 mt-5 rounded-lg">
                    Cerrar
                </flux:button>

            </div>
            @endif
        </div>
    </flux:modal>
    <flux:modal wire:model.self="showConfirmModalPDF" class="w-9/12 max-w-6xl p-6 rounded-2xl shadow-xl bg-white h-5/6"
        style="height:100vh">
        <div class="space-y-6 h-full flex flex-col">
            <div class="flex items-center space-x-4 border-b border-gray-200 pb-3">
                <flux:heading size="lg" class="text-gray-900 font-semibold">OFICIO CORRESPONDIENTE A ESTA SOLICITUD
                </flux:heading>
            </div>
            <div class="flex-1 overflow-auto">

                @if($pathPDF)
                    <iframe src="{{ $pathPDF}}" class="w-full h-full" frameborder="0">
                    </iframe>
                @else
                    <p class="text-red-500">No se encontró el PDF.</p>
                @endif

            </div>
            <div class="flex justify-end space-x-3 mt-4 border-t border-gray-200 pt-4">
                <flux:button variant="danger" wire:click="$set('showConfirmModalPDF', false)"
                    class="px-6 py-2 rounded-lg">
                    Cerrar
                </flux:button>
                <flux:button variant="primary" wire:click="descargarPDF" class="px-6 py-2 rounded-lg">
                    Descargar
                </flux:button>
            </div>
        </div>
    </flux:modal>


</div>