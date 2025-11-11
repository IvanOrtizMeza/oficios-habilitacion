<div>
    <!-- It is never too late to be what you might have been. - George Eliot -->
    <flux:table.columns
        class="rounded-lg bg-gray-100/70 dark:bg-neutral-800/80 backdrop-blur-sm sticky top-0 z-10 !px-6">
        @foreach ($columnas as $columna)
        <flux:table.column class="!px-6 !py-3 text-left font-semibold uppercase tracking-wider text-xs">
            {{ $columna }}
        </flux:table.column>
        @endforeach
    </flux:table.columns>
</div>