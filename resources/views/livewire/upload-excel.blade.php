
  <div >
       <flux:toast />
    <flux:accordion exclusive>
        <flux:accordion.item >
            <flux:accordion.heading>Apartado Base de datos federal</flux:accordion.heading>
        
            <flux:accordion.content>
                @if (!$excelFile)
                    <div x-data="{
                            isDragging: false,
                            progress: @entangle('progress'),
                            handleDrop(event) {
                                this.isDragging = false;
                                const file = event.dataTransfer.files[0];
                                if (file) $wire.upload('excelFile', file);
                            }
                        }" class="flex flex-col items-center justify-center">
                            <label
                                for="excelFileInput"
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="handleDrop($event)"
                                class="flex flex-col items-center justify-center w-70 h-56 border-2 border-dashed border-gray-300 hover:border-blue-500 transition p-4 rounded-lg cursor-pointer text-center"
                                >
                            
                                <svg class="w-10 h-10 text-green-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 16v-8m0 0l-3 3m3-3l3 3M4 16v4h16v-4" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14 2H6a2 2 0 00-2 2v16" />
                                </svg>

                                <p class="text-sm text-gray-600 text-center">
                                    Arrastra tu archivo aquí o <span class="text-emerald-600 font-medium">haz clic para seleccionar</span>
                                </p>
                                <p class="text-xs text-gray-400 mt-1">Formatos soportados: .xls, .xlsx</p>
                                <p class="text-xs text-gray-400 mt-1">Tamaño máximo: 2MB</p>
                            </label>
                            <div wire:loading wire:target="excelFile" class="w-full mt-4">
                                <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                                    <div class="h-2 bg-emerald-500 rounded-full animate-[progress_1s_ease-in-out_infinite]"></div>
                                </div>
                                <p class="text-sm text-gray-500 text-center mt-1">Subiendo archivo...</p>
                            </div>

                            <style>
                                @keyframes progress {
                                    0% { width: 0%; }
                                    50% { width: 70%; }
                                    100% { width: 100%; }
                                }
                            </style>
                            
                    </div>
                    {{-- Input oculto --}}
                    <input
                        id="excelFileInput"
                        type="file"
                        x-ref="file"
                        wire:model="excelFile"
                        accept=".xls,.xlsx"
                        class="hidden"
                    />
                @endif
                    {{-- Error --}}
                @error('excelFile')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
               
                {{-- Archivo seleccionado --}}
                @if ($excelFile)
                <h3 class="text-lg font-medium text-gray-800 mb-2">Archivo seleccionado</h3>
                    <div 
                        x-data="{ isUploading: false, progress: 0 }"
                        x-on:livewire-upload-start="isUploading = true"
                        x-on:livewire-upload-finish="isUploading = false; progress = 100"
                        x-on:livewire-upload-error="isUploading = false"
                        x-on:livewire-upload-progress="progress = $event.detail.progress" 
                        class="!border border-gray-300 rounded-lg shadow-sm overflow-hidden">
                        <flux:table>
                            <x-tables.table-flux-columns :columnas="['Archivo','Peso', 'Progreso', 'Acciones']" />
                            <flux:table.rows  class="rounded-lg divide-y divide-gray-200 dark:divide-neutral-700 bg-white/90 dark:bg-neutral-900/70 backdrop-blur-md">
                                <flux:table.row class="hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors duration-300">
                                    <flux:table.cell class="!px-6 py-4 whitespace-nowrap">
                                        {{ $excelFile->getClientOriginalName() }}
                                    </flux:table.cell>
                                    <flux:table.cell class="!px-6 py-4 whitespace-nowrap">
                                        {{ number_format($excelFile->getSize() / 1024, 1) }} KB
                                    </flux:table.cell>
                                    <flux:table.cell class="!px-6 py-4 whitespace-nowrap">
                                        <div x-show="isUploading" class="w-full">
                                                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden shadow-inner">
                                                    <div 
                                                        class="h-2.5 bg-gradient-to-r from-blue-500 via-indigo-500 to-blue-600 rounded-full transition-all duration-300 ease-in-out"
                                                        :style="`width: ${progress}%;`"
                                                    ></div>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1 text-center" x-text="`${progress}%`"></div>
                                            </div>
                                            <div x-show="!isUploading" x-transition.opacity.duration.500ms>
                                            <div class="relative w-full h-4 bg-gray-800 rounded-full overflow-hidden">
                                                    <div class="absolute inset-0 bg-gradient-to-r from-green-400 via-emerald-500 to-green-600 w-full animate-pulse"></div>
                                                    <span class="absolute inset-0 flex items-center justify-center text-xs text-white font-medium">100%</span>
                                                </div>
                                            </div>
                                            <flux:table.cell class="!px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-x-3">
                                                    <flux:button wire:click="subir" size="xs">Importar</flux:button>
                                                    <flux:button wire:click="removeFile" size="xs" variant="danger">
                                                        Eliminar
                                                    </flux:button>
                                                </div>
                                            </flux:table.cell>
                                    </flux:table.cell>
                                </flux:table.row>
                            </flux:table.rows>
                        </flux:table>
                    </div>
                    
                @endif

                {{-- Mensaje de éxito --}}
                @if (session()->has('message'))
                    <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                        {{ session('message') }}
                    </div>
                @endif
            </flux:accordion.content>
        </flux:accordion.item>

        <flux:accordion.item>
            <flux:accordion.heading>Do you offer any discounts for bulk purchases?</flux:accordion.heading>

            <flux:accordion.content>
                Yes, we offer special discounts for bulk orders. Please reach out to our sales team with your requirements.
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
  </div>