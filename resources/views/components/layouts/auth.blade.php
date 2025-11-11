{{-- heredo o mando informacion mediante el slot--}}
<x-layouts.auth.simple :title="$title ?? null">
    {{ $slot }}
</x-layouts.auth.simple>
{{-- hago uso de un componente auth.simple y le paso el titulo si es que existe, y el contenido del slot --}}