<div class="p-6" 
    x-data="{
        addRow() {
            const label = prompt('Introduce la etiqueta para la nueva fila (ej: <7000):');
            if (label) {
                this.$wire.addRow(label);
            }
        },
        addColumn() {
            const name = prompt('Introduce el nombre del nuevo país:');
            if (name) {
                this.$wire.addColumn(name);
            }
        }
    }">
    
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="#" icon="home" />
        <flux:breadcrumbs.item href="#">Maestros</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Tarifas Pick Up</flux:breadcrumbs.item>
    </flux:breadcrumbs>
    
    <div class="flex gap-2 mb-4 mt-4">
        {{-- <flux:button wire:click="save" color="primary">
            Guardar Cambios
        </flux:button> --}}
        <flux:spacer />
        <flux:button @click="addColumn()" color="success">
            Añadir País
        </flux:button>
        <flux:button @click="addRow()" color="info">
            Añadir Fila
        </flux:button>
    </div>

    <div x-show="$wire.table_columns.length > 1" class="overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                @foreach($table_columns as $colIndex => $column)
                    <flux:table.column align="center">
                        <div class="flex items-center justify-center gap-2">
                            @if ($colIndex === 0)
                                <span class="font-semibold">{{ $column['label'] }}</span>
                            @else
                                <input
                                    wire:model.defer="table_columns.{{ $colIndex }}.label"
                                    type="text"
                                    placeholder="Nombre País"
                                    class="flex-grow text-center font-semibold border-gray-300 text-gray-900 bg-gray-100 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                
                                <flux:button 
                                    wire:click="removeColumn({{ $column['id'] }})" 
                                    wire:confirm="¿Estás seguro de que quieres eliminar la columna '{{ $column['label'] }}' y todas sus tarifas?"
                                    size="xs" class="bg-red-500! flex-shrink-0" icon="x-mark" icon:variant="outline" />
                            @endif
                        </div>
                    </flux:table.column>
                @endforeach
                <flux:table.column align="center">Acciones</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($rows_data as $rowIndex => $row)
                    <flux:table.row align="center">
                        @foreach($table_columns as $colIndex => $column)
                            <flux:table.cell>
                                <input
                                    wire:model.defer="rows_data.{{ $rowIndex }}.{{ $column['name'] }}"
                                    type="text"
                                    placeholder="{{ $column['label'] }}"
                                    class="text-center border text-gray-900 bg-gray-50 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500
                                    {{ $colIndex === 0 ? 'font-semibold bg-gray-100 dark:bg-gray-800' : '' }}">
                            </flux:table.cell>
                        @endforeach
                        <flux:table.cell>
                            {{-- Solo mostrar el botón de borrar si la fila no es 'Mínima' --}}
                            @if($row['tier_id'] !== null)
                                <flux:button 
                                    wire:click="removeRow({{ $row['tier_id'] }})"
                                    wire:confirm="¿Estás seguro de que quieres eliminar la fila '{{ $row['tier_label'] }}'?"
                                    size="sm" class="bg-red-500!" icon="x-mark" icon:variant="outline" />
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>

    <div x-show="$wire.table_columns.length <= 1" class="text-center py-8">
        <p class="text-gray-500">No se encontraron datos para el tipo de servicio "{{ $serviceTypeName }}".</p>
        <p class="text-gray-400 text-sm mt-2">Puedes empezar añadiendo un nuevo país.</p>
    </div>
</div>
