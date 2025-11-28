<div>
    <flux:toast />
    @if (!$excelFile)

        <div>

            <div>
                <flux:file-upload wire:model="excelFile">
                    <flux:file-upload.dropzone>
                        <x-slot:heading>
                            Arrastra tu archivo aquí o
                            <span class="text-emerald-600 font-medium">haz clic para seleccionar</span>
                        </x-slot:heading>
                        <x-slot:text>
                            Formatos soportados: .xls, .xlsx
                        </x-slot:text>
                    </flux:file-upload.dropzone>
                </flux:file-upload>
            </div>

            <div class="flex flex-col items-center justify-center mt-7">
                <div wire:loading wire:target="excelFile" class=" bg-white/50 dark:bg-black/40 z-50">

                    <span class="text-gray-700 dark:text-gray-200 font-medium mt-7">Cargando archivo...</span>
                </div>
            </div>

        </div>

    @endif
    @if ($excelFile)
        <flux:separator text="Archivo seleccionado" />
        <br>
        {{-- <h3 class="text-lg font-medium text-gray-800 mb-2">Archivo seleccionado</h3>--}}
        <flux:table>
            <x-tables.table-flux-columns :columnas="['Archivo', 'Peso', 'Progreso', 'Acciones']" />

            <flux:table.rows
                class="rounded-lg divide-y divide-gray-200 dark:divide-neutral-700 bg-white/90 dark:bg-neutral-900/70 backdrop-blur-md">
                <flux:table.row class="hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors duration-300">

                    {{-- Nombre del archivo --}}
                    <flux:table.cell class="!px-6 py-4 whitespace-nowrap">
                        {{ $excelFile->getClientOriginalName() }}
                    </flux:table.cell>

                    {{-- Tamaño del archivo --}}
                    <flux:table.cell class="!px-6 py-4 whitespace-nowrap">
                        {{ number_format($excelFile->getSize() / 1024, 1) }} KB
                    </flux:table.cell>

                    {{-- Barra de progreso --}}
                    <flux:table.cell class="!px-6 py-4 whitespace-nowrap">
                        <div class="w-full">
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden shadow-inner">
                                <div
                                    class="h-3 bg-gradient-to-r from-green-400 via-emerald-500 to-green-600 w-full animate-pulse rounded-full transition-all duration-500 ease-in-out">
                                </div>
                            </div>
                            <div class="text-xs text-gray-700 dark:text-gray-200 mt-1 text-center font-medium">
                                100%</div>
                        </div>
                    </flux:table.cell>

                    {{-- Acciones --}}
                    <flux:table.cell class="!px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-x-3">
                            <flux:button wire:click="subir" size="xs">Importar</flux:button>
                            <flux:button wire:click="removeFile" size="xs" variant="danger">Eliminar
                            </flux:button>
                        </div>
                    </flux:table.cell>

                </flux:table.row>
            </flux:table.rows>
        </flux:table>

    @endif
   {{--   <flux:accordion exclusive>
        <flux:accordion.item>
            <flux:accordion.heading>Apartado Base de datos federal</flux:accordion.heading>

            <flux:accordion.content>



            </flux:accordion.content>
        </flux:accordion.item>

    </flux:accordion>
    --}}
    <flux:modal wire:model="showErrorsModal" class="md:w-96">
        <div class="flex items-center border-b border-gray-200 pb-3 space-y-6 mb-4">
            <flux:heading size="lg">Errores encontrados en tu archivo Excel</flux:heading>
        </div>
        <div class="space-y-2">
            @foreach($erroresList as $error)
                <p class="text-sm">{{ $error }}</p>
            @endforeach
        </div>

    </flux:modal>
</div>