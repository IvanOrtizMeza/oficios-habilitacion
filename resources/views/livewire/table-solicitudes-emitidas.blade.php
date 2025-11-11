<div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <flux:input type="text" placeholder="Buscar Solicitud..." wire:model.live="search" />
        <flux:input type="date" placeholder="Selecciona una fecha" />
    </div>
    <flux:table class="w-full mt-10">
        <flux:table.columns>
            <flux:table.column>Nombre</flux:table.column>
            <flux:table.column>Rfc</flux:table.column>
            <flux:table.column>Curp</flux:table.column>
            <flux:table.column>Status</flux:table.column>
        </flux:table.columns>
        <flux:table.rows class="divide-y bg-white border-t">
            @foreach($solicitudes as $oficio)
            <flux:table.row :key="$oficio->id">



                <flux:table.cell>{{ $oficio->nombre }}</flux:table.cell>
                <flux:table.cell>{{ $oficio->rfc }}</flux:table.cell>
                <flux:table.cell>{{ $oficio->curp }}</flux:table.cell>
                <flux:table.cell>
                    <flux:badge size="sm" :color="'green'" inset="top bottom">
                        {{ $oficio->estado }}
                    </flux:badge>
                </flux:table.cell>
                <flux:table.cell>
                    <flux:modal.trigger>
                        <flux:button>
                            Ver
                        </flux:button>
                    </flux:modal.trigger>
                </flux:table.cell>
            </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
    <div class="mt-4">
        {{ $solicitudes->links() }}
    </div>
</div>