<div>
    <div class="relative mb-6 w-full flex items-center justify-between">
        <!-- Heading a la izquierda -->
        <flux:heading size="xl" level="1">{{ __('Settings') }}</flux:heading>
        <!-- Tabs a la derecha -->
        <flux:tabs class="px-4" wire:model="activeTab">
            <flux:tab  name="solicitudes" wire:click="selectTab('solicitudes')" >Solicitudes <span> |</span></flux:tab>
            <flux:tab  name="emitidas">Emitidas</flux:tab>
            <flux:tab name="pendientes">Pendientes</flux:tab>
        </flux:tabs>

       
    </div>

    <!-- Separador debajo -->
    <flux:separator variant="subtle" />               
</div>
