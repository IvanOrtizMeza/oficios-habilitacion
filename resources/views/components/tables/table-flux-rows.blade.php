<div>
    <flux:table.rows
        class="rounded-lg divide-y divide-gray-200 dark:divide-neutral-700 bg-white/90 dark:bg-neutral-900/70 backdrop-blur-md">
        @foreach($datos as $oficio)
        
        <flux:table.row :key="$oficio->id"
            class="hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors duration-300">

            <flux:table.cell class="!px-6 py-4 whitespace-nowrap">
                {{ $oficio->nombre }}
            </flux:table.cell>
            {{-- RFC --}}
            <flux:table.cell class="!px-6 py-4 whitespace-nowrap">
                {{ $oficio->rfc }}
            </flux:table.cell>

            {{-- CURP --}}
            <flux:table.cell class="!px-6 py-4 whitespace-nowrap">
                {{ $oficio->curp }}
            </flux:table.cell>
            <flux:table.cell class="!px-6 py-4 whitespace-nowrap">
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
                    {{$oficio->statusSolicitud->nombre}}
                </flux:badge>
            </flux:table.cell>
            <flux:table.cell class="px-6 py-4 text-center">
                <div class="flex justify-center gap-2">
                    <flux:button icon="eye" size="xs" color="primary" wire:click="$dispatch('ModalSolicitud', { id: {{ $oficio->id }} })" />
                    <flux:button icon="pencil" size="xs" variant="outline" wire:click="editar({{ $oficio->id }})" />
                    <flux:button icon="trash" size="xs" color="danger" wire:click="eliminar({{ $oficio->id }})" />
                </div>
            </flux:table.cell>
        </flux:table.row>
        @endforeach
    </flux:table.rows>
</div>