<div class="p-6">
    {{-- Título y Breadcrumbs --}}
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="#" icon="home" />
        <flux:breadcrumbs.item href="#">Maestros</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $serviceTypeName }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading class="mt-2" size="xl">Gestión de {{ $serviceTypeName }}</flux:heading>
    
    {{-- Formulario para añadir nuevo item --}}
    <div class="mt-6 p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
        <form wire:submit="addNewItem" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <flux:field label="Etapa (Gasto)">
                <flux:select wire:model="newStage">
                    <flux:select.option value="Origen">Origen</flux:select.option>
                    <flux:select.option value="Destino">Destino</flux:select.option>
                </flux:select>
            </flux:field>
            <flux:field label="Concepto del Costo">
                <flux:input wire:model="newConcept" placeholder="Ej. Transporte Nacional" />
                <flux:error name="newConcept" />
            </flux:field>
            <div class="md:col-span-2 flex justify-end">
                <flux:button type="submit" icon="plus">Añadir Concepto</flux:button>
            </div>
        </form>
    </div>

    {{-- Tabla de Costos --}}
    <div class="mt-6 w-full overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Concepto</flux:table.column>
                <flux:table.column>Monto Fijo</flux:table.column>
                <flux:table.column>Monto Variable (%)</flux:table.column>
                <flux:table.column>Mínima</flux:table.column>
                <flux:table.column>Moneda</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>
            
            <flux:table.rows>
                @forelse($groupedItems as $stage => $items)
                    {{-- Fila de cabecera para cada grupo --}}
                    <flux:table.row class="bg-gray-100 dark:bg-gray-700">
                        <flux:table.cell colspan="6" class="font-bold text-lg">
                            Gastos en {{ $stage }}
                        </flux:table.cell>
                    </flux:table.row>

                    @foreach($items as $index => $item)
                        <flux:table.row wire:key="item-{{ $item->id }}">
                            <flux:table.cell class="font-semibold">{{ $item->concept }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:input type="number" step="0.01" wire:model.blur="costItems.{{ $index }}.fixed_amount" />
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:input type="number" step="0.0001" wire:model.blur="costItems.{{ $index }}.variable_rate" placeholder="ej. 0.0025 para 0.25%" />
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:input type="number" step="0.01" wire:model.blur="costItems.{{ $index }}.minimum_charge" />
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:select wire:model.blur="costItems.{{ $index }}.currency_id">
                                    <flux:select.option value="">N/A</flux:select.option>
                                    @foreach($currencies as $currency)
                                        <flux:select.option value="{{ $currency->id }}">{{ $currency->code }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button variant="danger" size="sm" icon="trash" 
                                    wire:click="removeItem({{ $item->id }})"
                                    wire:confirm="¿Estás seguro de eliminar el concepto '{{ $item->concept }}'?"
                                />
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center text-gray-500 py-4">
                            No hay conceptos de costo definidos. Empieza añadiendo uno nuevo.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>