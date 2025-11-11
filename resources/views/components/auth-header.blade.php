{{-- Componente de encabezado para las páginas de autenticación --}}
{{-- Recibe dos PROPS propiedades: title y description --}}
{{-- Muestra el título y la descripción centrados en la página --}}
{{-- Uso de componentes Flux para los estilos de los textos --}}
@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <flux:heading size="xl">{{ $title }}</flux:heading>
    <flux:subheading>{{ $description }}</flux:subheading>
</div>
