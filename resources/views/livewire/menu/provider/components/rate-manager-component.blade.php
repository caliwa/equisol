<div 
x-data="{
        tarifModalParam: null,
        tarifModalMethod: null,
        {{-- parseAndSetWeight(inputValue, rowIndex) {
            $wire.ZoneIdRate = rowIndex;
            $flux.modal('operand-modal').show();
        }, --}}

        originalWeight: null,
        currentWeight: null,
        previousWeight: null,
        nextWeight: null,
        parseAndSetWeight(prev, current, next) {
            console.log(prev, current, next);
            $wire.originalWeight = parseFloat(current);
            $wire.currentWeight = parseFloat(current);
            $wire.previousWeight = parseFloat(prev);
            // Si next es el string 'null', lo convertimos al valor null de JS
            $wire.nextWeight = (next === 'null') ? null : parseFloat(next);
            $flux.modal('edit-weight-modal').show();
        },
        isLoadingWeightFlyoutModal: false,
    }"
    class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">

<flux:modal
    x-on:close="
        isLoadingWeightFlyoutModal = false;
        escapeEnabled = true;
        $wire.originalWeight = null;
        $wire.newWeight = null;
        $wire.currentWeight = null;
        $wire.previousWeight = null;
        $wire.nextWeight = null;
        $wire.resetValidationWrapper();"
    x-on:x-unblock-weight-flyout-modal.window="
        isLoadingWeightFlyoutModal = false;
    "
    name="edit-weight-modal" variant="flyout">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Editar Peso</flux:heading>
                <flux:text class="mt-2">
                    Modifica el valor del peso. El nuevo valor debe respetar los límites.
                </flux:text>
            </div>
            
            {{-- NUEVO DISEÑO ESTILO MEDIDOR VERTICAL --}}
            <div class="max-w-xs mx-auto space-y-3 text-center">

                <div>
                    <flux:label>Máximo</flux:label>
                    <div class="mt-1 flex items-center justify-center rounded-md bg-gray-800 p-3 text-2xl font-semibold text-white font-mono tracking-wider">
                        <template x-if="$wire.nextWeight !== null">
                            <span x-text="$wire.nextWeight"></span>
                        </template>
                        <template x-if="$wire.nextWeight === null">
                            <span>&infin;</span>
                        </template>
                    </div>
                </div>

                <div class="py-2">
                    <flux:field>
                        <flux:label>Nuevo Peso (KG)</flux:label>
                        <flux:input
                            wire:model="newWeight"
                            type="number"
                            step="0.5"
                            {{-- Clases para hacerlo el elemento principal, con ! para forzar el estilo --}}
                            class="!text-4xl !font-bold !text-center !text-lime-600 !h-16"
                        />
                        <flux:error name="newWeight" />
                    </flux:field>
                </div>

                <div>
                    <flux:label>Mínimo</flux:label>
                    <div class="mt-1 flex items-center justify-center rounded-md bg-gray-800 p-3 text-2xl font-semibold text-white font-mono tracking-wider"
                         x-text="$wire.previousWeight ?? 'N/A'">
                    </div>
                </div>

            </div>
            {{-- FIN DE LA SECCIÓN MODIFICADA --}}

            <div class="flex">
                <flux:button 
                    @click="blockInteractions($event);"
                    x-on:click="
                        isLoadingWeightFlyoutModal = true; 
                        $wire.updateWeight();
                    "
                    variant="primary"
                    color="lime"
                >
                    <template x-if="isLoadingWeightFlyoutModal">
                        <flux:icon.loading />
                    </template>
                    <template x-if="!isLoadingWeightFlyoutModal">
                        <span>Modificar Peso</span>
                    </template>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal
        x-data="{ isLoadingTariffModal: false }" 
        x-on:x-unblock-loading-tariff-modal.window="
            isLoadingTariffModal = false;
        "
        name="tariff-modal" class="min-w-[22rem]"
        x-on:close="
            isLoadingTariffModal = false;
            escapeEnabled = true;
            $wire.numericPriceRate = null;
            $wire.ZoneIdRate = null;
            $wire.resetValidationWrapper();">
        <flux:badge color="lime">Modificación</flux:badge> Cotizador FI

        <div class="space-y-6 mt-6">
        <flux:callout variant="warning" icon="exclamation-circle" heading="Ten en cuenta que cualquier cambio alterará los cálculos futuros del Cotizador FI" />

            <div>
                <flux:input 
                @keydown.enter="blockInteractions($event); isLoadingTariffModal = true;"
                wire:model="numericPriceRate" 
                type="number"
                step="0.01"
                icon="currency-dollar" placeholder="Tarifa" label="Tarifa para la zona seleccionada"/>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button
                    @click="blockInteractions($event);" x-on:click="
                    {{-- $wire.addRowTariff(); --}}
                    isLoadingTariffModal = true; 
                    $wire.updateRate();
                    "
                    variant="primary" color="green">
                    <template x-if="isLoadingTariffModal">
                        <flux:icon.loading />
                    </template>

                    <template x-if="!isLoadingTariffModal">
                        <span>Modificar Precio</span>
                    </template>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:heading size="md">Gestión de Tarifas</flux:heading>

    <div class="w-full overflow-x-auto">
        <div class="min-w-max">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Peso (KG)</flux:table.column>
                    @foreach($zones as $zone)
                        <flux:table.column align="center">
                            
                            <flux:badge color="zinc">Zona {{ $zone }}</flux:badge>
                        </flux:table.column>
                    @endforeach
                    <flux:table.column align="center">
                        <flux:badge icon="user-circle" color="rose">Acciones</flux:badge>
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($ratesByWeight as $weight => $rates)
                    <flux:table.row wire:key="rate-weight-{{ $weight }}">
                        <flux:table.cell class="font-semibold text-gray-800 dark:text-gray-200">
                            
                            <flux:tooltip content="Editar Peso (KG)">
                                <flux:badge color="lime" class="cursor-pointer" variant="pill" icon="scale"
                                    @click="
                                        parseAndSetWeight(
                                            '{{ $weightKeys[$loop->index - 1] ?? 0 }}',
                                            '{{ $weight }}',                            
                                            '{{ $weightKeys[$loop->index + 1] ?? 'null' }}'
                                        );
                                    "
                                >
                                    {{ $weight }}
                                </flux:badge>
                            </flux:tooltip>
                            
                        </flux:table.cell>
                            @foreach($zones as $zone)
                                <flux:table.cell align="center">
                                    @if(isset($rates[$zone]))
                                        <flux:tooltip flux:tooltip content="Oprime para configurar Tarifa" position="bottom">
                                            <flux:button icon="swatch"
                                                class="w-32"
                                                placeholder="Peso"
                                                @click="
                                                    $wire.numericPriceRate = {{$rates[$zone]->price}};
                                                    $wire.ZoneIdRate = {{$rates[$zone]->id}};
                                                    {{-- $wire.set('tarifModalParam', {{ json_encode([
                                                            'numericPriceRate' => $rates[$zone]->price,
                                                            'columnNameTariff' => $rates[$zone]->id
                                                        ]) 
                                                    }}); --}}
                                                    $flux.modal('tariff-modal').show();
                                                "
                                              
                                                {{-- type="number"  --}}
                                                {{-- step="0.01" --}}
                                                {{-- value="{{ $rates[$zone]->price }}"  --}}
                                                {{-- placeholder="{{ $rates[$zone]->price }}"  --}}
                                                {{-- wire:change="updateRate({{ $rates[$zone]->id }}, $event.target.value)" --}}
                                            >
                                                {{ $rates[$zone]->price }}
                                            </flux:button>
                                        </flux:tooltip>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                    @endif
                                </flux:table.cell>
                            @endforeach
                            <flux:table.cell align="end">
                                <flux:button
                                    size="sm"
                                    variant="primary"
                                    color="red"
                                    @click="prepareDichotomic({
                                        method: 'deleteRateRow',
                                        param: '{{ $weight }}',
                                        heading: 'Eliminar Peso',
                                        message: `¿Estás seguro de eliminar toda la fila del peso '{{ $weight }}'?`,
                                        modalDichotomicBtnText: 'Borrar'
                                    })"
                                >
                                    Eliminar Peso
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="{{ count($zones) + 2 }}">
                                <div class="text-center py-8">
                                    <p class="text-gray-500 dark:text-gray-400">No hay tarifas configuradas.</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>

                <tfoot class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <td class="p-2">
                            <flux:input type="number" step="0.5" wire:model="newWeight" size="sm" class="!font-bold" placeholder="Nuevo Peso" />
                        </td>
                        <td class="p-2 text-right">
                            <flux:button wire:click="addRateRow" size="sm" variant="primary" icon="plus">Añadir Fila</flux:button>
                        </td>
                    </tr>
                </tfoot>
            </flux:table>
        </div>
    </div>
</div>