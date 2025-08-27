<div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
    <flux:modal
        x-data="{ isLoadingTariffModal: false }" 
        x-on:x-unblock-loading-tariff-modal.window="
            isLoadingTariffModal = false;
        "
        name="tariff-modal" class="min-w-[22rem]"
        x-on:close="
            isLoadingTariffModal = false;
            escapeEnabled = true;
            $wire.numericValueTariff = null;
            $wire.rowIndexTariff = null;
            $wire.columnNameTariff = null;
            $wire.resetValidationWrapper();">
        <flux:badge color="lime">Modificación</flux:badge> Cotizador FI

        <div class="space-y-6 mt-6">
        <flux:callout variant="warning" icon="exclamation-circle" heading="Ten en cuenta que cualquier cambio alterará los cálculos futuros del Cotizador FI" />

            <div>
                <flux:input 
                @keydown.enter="blockInteractions($event); isLoadingTariffModal = true; $wire.addRowTariff();"
                wire:model="numericValueTariff" type="number" icon="currency-dollar" placeholder="Tarifa" label="Tarifa para la zona seleccionada"/>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button
                    @click="blockInteractions($event);" x-on:click="isLoadingTariffModal = true; $wire.addRowTariff();" variant="primary" color="green">
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
                                
                                <flux:tooltip flux:tooltip content="Oprime para configurar Tarifa" placement="top">
                                    <flux:badge color="lime" class="cursor-pointer" variant="pill" icon="scale">
                                        {{ $weight }}
                                    </flux:badge>
                                </flux:tooltip>

                            </flux:table.cell>
                            @foreach($zones as $zone)
                                <flux:table.cell align="center">
                                    @if(isset($rates[$zone]))
                                        <flux:tooltip flux:tooltip content="Oprime para configurar Peso (KG)" position="bottom">
                                            <flux:button icon="percent-badge"
                                                class="w-32"
                                                x-bind:disabled="isDisabledOpenTariffModal"
                                                placeholder="Peso"
                                                @click="
                                                        {{-- $wire.numericValueTariff = {{$rows_data[$rowIndex][$column['name']]}};
                                                        $wire.rowIndexTariff = {{ $rowIndex }};
                                                        $wire.columnNameTariff = '{{ $column['name'] }}'; --}}
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