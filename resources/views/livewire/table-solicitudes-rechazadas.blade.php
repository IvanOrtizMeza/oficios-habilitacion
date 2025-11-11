<div class="p-1">
    <div class="mb-4">
        <p>IDs seleccionados: {{ implode(', ', $selected) }}</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2 mt-2">
        <flux:input type="text" placeholder="Buscar Solicitud..." wire:model.live="search" />
        <flux:input type="date" placeholder="Selecciona una fecha" />
    </div>

    <div class="!border border-gray-300 rounded-lg shadow-sm overflow-hidden">
        <flux:table>
            <div>
                <flux:table.columns
                    class="rounded-lg bg-gray-100/70 dark:bg-neutral-800/80 backdrop-blur-sm sticky top-0 z-10 !px-6">
                    <flux:table.column class="!px-6 !py-3 text-left">
                        <flux:checkbox wire:model.live="selectAll" />
                    </flux:table.column>
                    <flux:table.column class=" !px-6 !py-3 text-left font-semibold uppercase tracking-wider text-xs">
                        nombre
                    </flux:table.column>
                    <flux:table.column class="!px-6 !py-3 text-left font-semibold uppercase tracking-wider text-xs">Rfc
                    </flux:table.column>
                    <flux:table.column class="!px-6 !py-3 text-left font-semibold uppercase tracking-wider text-xs">Curp
                    </flux:table.column>
                    <flux:table.column class="!px-6 !py-3 text-left font-semibold uppercase tracking-wider text-xs">
                        estado
                    </flux:table.column>
                    <flux:table.column class="!px-6 !py-3 text-center font-semibold uppercase tracking-wider text-xs">
                        acciones
                    </flux:table.column>
                </flux:table.columns>
            </div>
            <div>
                <flux:table.rows
                    class="rounded-lg divide-y divide-gray-200 dark:divide-neutral-700 bg-white/90 dark:bg-neutral-900/70 backdrop-blur-md">
                    @foreach($solicitudes as $oficio)
                    <flux:table.row :key="$oficio->id"
                        class="hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors duration-300">
                        <flux:table.cell class="!px-6 py-4 whitespace-nowrap">
                            <flux:checkbox wire:model.live="selected" :value="$oficio->id" />
                        </flux:table.cell>
                        <flux:table.cell class="!px-6 py-4 whitespace-nowrap">{{ $oficio->nombre }}</flux:table.cell>
                        <flux:table.cell class="!px-6 py-4 whitespace-nowrap">{{ $oficio->rfc }}</flux:table.cell>
                        <flux:table.cell class="!px-6 py-4 whitespace-nowrap">{{ $oficio->curp }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{
                                    $oficio->estado === 0 ? 'blue' :
                                    ($oficio->estado === 1 ? 'green' :
                                    ($oficio->estado === 2 ? 'red' : 'gray'))
                                    }}" size="sm" inset="top bottom"
                                class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium">
                                <span class="size-2 rounded-full 
                                    {{ 
                                        $oficio->estado === 0 
                                            ? 'bg-blue-500' : 
                                        ($oficio->estado === 1 
                                            ? 'bg-green-500' : 
                                        ($oficio->estado === 2 
                                            ? 'bg-red-500' : 
                                            'bg-gray-400'))
                                    }}
                                "></span>
                                {{ 
                                    $oficio->estado == 0 ? 'Pendiente' : 
                                    ($oficio->estado == 1 ? 'Habilitado' : 
                                    ($oficio->estado == 2 ? 'No Habilitado' : 'Desconocido')) 
                                }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                <flux:button icon="eye" size="xs" color="primary" wire:click="ver({{ $oficio->id }})" />
                                <flux:button icon="trash" size="xs" color="danger"
                                    wire:click="eliminar({{ $oficio->id }})" />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </div>
        </flux:table>
    </div>
    <div class="mt-4 flex justify-start">
        {{ $solicitudes->links() }}
    </div>
</div>