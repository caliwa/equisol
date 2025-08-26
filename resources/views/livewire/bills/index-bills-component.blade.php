<div x-data="{
    isDisabledCalculationStrategyModal: false,

}"
x-on:x-block-open-quote-generic-figure-modal.window="
    isDisabledCalculationStrategyModal = true;
"
x-on:x-unblock-open-quote-generic-figure-modal.window="
    isDisabledCalculationStrategyModal = false;
"
 class="p-6">

    {{-- Título y Breadcrumbs --}}
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="#" icon="home" />
        <flux:breadcrumbs.item href="#">Maestros</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $serviceTypeName }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading class="mt-2" size="xl">Gestión Base de datos - Maestro Gastos</flux:heading>
    <div wire:dirty>Esperando sincronización...</div> 
    <div wire:dirty.remove>Los cambios están sincronizados.</div>

    <flux:radio.group 
            wire:model="type_service" 
            label="Seleccione un Maestro" 
            variant="pills"
            class="flex-wrap"
            @change="loadingSpinner($event);"
        >
        <flux:radio wire:click="SelectMasterTypeService('Gastos Mar')" value="g_mar" label="Gastos Mar" />
        <flux:radio wire:click="SelectMasterTypeService('Gastos Aéreo')" value="g_aereo" label="Gastos Aéreo" />
        <flux:radio wire:click="SelectMasterTypeService('Gastos Courier')" value="g_courier" label="Gastos Courier" />
    </flux:radio.group>

    
    {{-- Formulario para añadir nuevo item --}}
    <div class="mt-6 p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <flux:field label="Etapa (Gasto)">
                <flux:select wire:model="newStage">
                    <flux:select.option value="Origen">Origen</flux:select.option>
                    <flux:select.option value="Destino">Destino</flux:select.option>
                </flux:select>
            </flux:field>
            <flux:field label="Concepto del Costo">
                <flux:input wire:model="newConcept" placeholder="Ej. Transporte Nacional" />
            </flux:field>
            <div class="md:col-span-2 flex justify-end">
                <flux:button @click="loadingSpinner($event);" wire:click="addNewItem"  icon="plus">Añadir Concepto</flux:button>
            </div>
        </div>
    </div>

    {{-- Tabla de Costos --}}
    <div class="mt-6 w-full overflow-x-auto">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Concepto</flux:table.column>
                <flux:table.column>Moneda</flux:table.column>
                <flux:table.column>Fórmula</flux:table.column>
                <flux:table.column>Acciones</flux:table.column>
            </flux:table.columns>
            
            <flux:table.rows>
                @php
                    $index = 0;
                @endphp
                @forelse($groupedItems as $stage => $items)
                    {{-- Fila de cabecera para cada grupo --}}
                    <flux:table.row class="text-center bg-gray-100 dark:bg-gray-700">
                        <flux:table.cell colspan="4" class="font-bold text-lg">
                            Gastos en {{ $stage }}
                        </flux:table.cell>
                    </flux:table.row>

                    @foreach($items as $item)

                        <flux:table.row class="text-center" wire:key="item-{{ $item['id'] }}">
                            <flux:table.cell class="font-semibold">{{ $item['concept'] }}</flux:table.cell>

                            <flux:table.cell>
                                <flux:select 
                                    x-on:change="loadingSpinner($event); $wire.saveNewCurrencyMaster({{$index}});"
                                    wire:model="costItems.{{ $index }}.currency_id">
                                    <flux:select.option value="">N/A</flux:select.option>
                                    @foreach($currencies as $currency)
                                        <flux:select.option value="{{ $currency['id'] }}">{{ $currency['code'] }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:table.cell>
                                                        {{-- Celda con input para la fórmula --}}
                            <flux:table.cell>

                                {{-- <template x-for="(responsabilidad, index) in responsabilidades" :key="index"> --}}
                                <div class="flex-1 relative">
                                    <flux:input  
                                        {{-- x-model="responsabilidades[index]"  --}}
                                        wire:model="costItems.{{ $index }}.formula"
                                        readonly
                                        type="text"
                                        class="pointer-events-none"
                                        {{-- :placeholder="'Responsabilidad #' + (index + 1)" --}}
                                        placeholder="ej. max(monto * 0.0025, 348900)"
                                        />
                                    
                                    <flux:button
                                        @click="blockInteractions($event)"
                                        icon="calculator"
                                        icon:variant="outline"
                                        x-bind:disabled="isDisabledCalculationStrategyModal"
                                        x-bind:class="[
                                            'absolute! top-0! right-0! rounded-r-md!',
                                            isDisabledCalculationStrategyModal ? 'opacity-30 pointer-events-none animate-pulse' : ''
                                        ]"
                                        wire:click="openCalculationStrategyModal({{ $index }})"
                                    >
                                    </flux:button>
                                </div>

                                        
                                {{-- </template> --}}
                                {{-- <flux:input type="text" wire:model.blur="costItems.{{ $index }}.formula"  /> --}}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:tooltip content="Borrar columna" position="top">
                                    <flux:button variant="danger" size="xs"
                                        icon="x-mark"
                                        icon:variant="outline"
                                        wire:click="removeItem({{ $item['id'] }})"
                                        wire:confirm="¿Estás seguro de eliminar el concepto '{{ $item['concept'] }}'?"
                                    />
                                </flux:tooltip>

                            </flux:table.cell>
                        </flux:table.row>
                        @php $index++; @endphp 
                    @endforeach
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-gray-500 py-4"> {{-- <-- Colspan actualizado a 7 --}}
                            No hay conceptos de costo definidos. Empieza añadiendo uno nuevo.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>