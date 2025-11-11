<flux:modal
    name="edit-profile"
    class="w-full md:w-6xl"
    
>
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{ $modalTitle }} </flux:heading>
            <flux:text class="mt-2">Datos recibidos del oficio.</flux:text>
           
        </div>
           <flux:input 
                label="Name" 
                placeholder="Your name" 
                wire:model="form.nombre"
            />
            <flux:input 
                label="Descripcion" 
                placeholder="Descripcion" 
                wire:model="form.descripcion"
            />
            <flux:input 
                label="Tipo" 
                placeholder="Tipo" 
                wire:model="form.tipo"
            />
        
        <div class="flex gap-2">
            <flux:spacer />
            <flux:button type="button" variant="primary" wire:click="cerrar">Cerrar</flux:button>
            <flux:button type="submit" variant="danger" wire:click="firmar">Firmar</flux:button>
        </div>
    </div>
</flux:modal>
