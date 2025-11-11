<x-layouts.app.sidebar :title="$title ?? null"> <!-- aqui se encapsula el sidebar (barra lateral) y aparte le mando el dashboard-->
    <!--encapsula el area pricipal de la pagina area central del dashboard-->
    <flux:main>
        <!-- se inyecta el contenido que viene de dashboard vista anterior-->
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>


<!--en resumen aqui se encapsula el sidebar y el area principal del dashboard junto con el slot de informacion del dashboard-->