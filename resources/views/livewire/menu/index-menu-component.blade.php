<div class="p-6" 
    x-data="{
        parseAndSetTariff(inputValue, rowIndex, columnName) {
            const regex = /^(<=|>=|<|>)\D*(-?[\d.]+)/;
            const matches = inputValue.match(regex);

            if (matches) {
                // matches[1] es el operador (ej: '<=')
                // matches[2] es el número (ej: '50')
                this.$wire.set('selectedOperator', matches[1]);
                this.$wire.set('numericValueTariff', matches[2]);
                this.$wire.set('rowIndexTariff', rowIndex);
                this.$wire.set('columnNameTariff', columnName);
            } else {
                console.warn('El formato de la tarifa no es válido:', inputValue);
            }
        }

    }">
    
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="#" icon="home" />
        <flux:breadcrumbs.item href="#">Maestros</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $serviceTypeName }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    {{-- <flux:modal name="dichotomic-modal" class="min-w-[22rem]" :dismissible="false"> --}}
    <flux:modal
        x-data="{ isLoadingDichotomicModal: false }" 
        name="dichotomic-modal" class="min-w-[22rem]" x-on:close="isLoadingDichotomicModal = false; escapeEnabled = true;">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" x-text="modalDichotomicHeading"></flux:heading>
                <flux:text class="mt-2">
                    <span x-text="modalDichotomicMessage"></span>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>
                <flux:button @click="blockInteractions($event)" x-on:click="isLoadingDichotomicModal = true; $wire.call(modalDichotomicMethod, modalDichotomicParam)" variant="danger">
                    <template x-if="isLoadingDichotomicModal">
                        <flux:icon.loading />
                    </template>

                    <template x-if="!isLoadingDichotomicModal">
                        <span>Borrar</span>
                    </template>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal 
    name="operand-modal" variant="flyout" 
    x-on:close="
        escapeEnabled = true;
        $wire.selectedOperator = '';
        $wire.numericValueTariff = null;
        $wire.rowIndexTariff = null;
        $wire.columnNameTariff = null;
        $wire.resetValidationWrapper();">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tarifa</flux:heading>
                <flux:text class="mt-2">Creación de fila para representar una nueva tarifa</flux:text>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <flux:select wire:model.change="selectedOperator" variant="listbox" label="Operador Relacional" placeholder="Seleccione...">
                        <flux:select.option value="<">
                            <div class="flex items-center gap-2">
                                <flux:icon.chevron-left variant="solid" class="text-blue-500 dark:text-blue-300" /> Menor
                            </div>
                        </flux:select.option>
                        <flux:select.option value=">">
                            <div class="flex items-center gap-2">
                                <flux:icon.chevron-right variant="solid" class="text-blue-500 dark:text-blue-300" /> Mayor
                            </div>
                        </flux:select.option>
                        <flux:select.option value="<=">
                            <div class="flex items-center gap-2">
                                <flux:icon.chevron-left variant="solid" class="text-blue-500 dark:text-blue-300" /> 
                                <flux:icon.equals variant="solid" class="text-blue-500 dark:text-blue-300" />
                                Menor o igual
                            </div>
                        </flux:select.option>
                        <flux:select.option value=">=">
                            <div class="flex items-center gap-2">
                                <flux:icon.chevron-right variant="solid" class="text-blue-500 dark:text-blue-300" /> 
                                <flux:icon.equals variant="solid" class="text-blue-500 dark:text-blue-300" />
                                Mayor o igual
                            </div>
                        </flux:select.option>
                    </flux:select>
                    <br/>
                    <flux:description>Si está editando esta tarifa, <br/>no guarde si no ha ingresado un <br/>valor diferente al original<br/> seleccionado.</flux:description>

                </div>


                <div>
                    <flux:input wire:model="numericValueTariff" 
                    type="number"
                    label="Valor numérico" placeholder="Ingrese un valor" />
                    <br/>
                    <flux:description>Debe ser un valor mayor a 0.</flux:description>
                </div>

            </div>

            <div class="flex mb-2">
                <flux:button 
                    wire:click="addRow()"
                    @click="blockInteractions($event)"
                    variant="primary"
                >
                    Crear tarifa
                </flux:button>
            </div>
        </div>
        <flux:callout icon="sparkles" color="purple">
            <flux:callout.heading>Representación numérica</flux:callout.heading>
            <flux:callout.text>
                El resultado quedará "concatenado" con el valor de la columna, por ejemplo:
                <code class="text-sm">"<" + 7000</code> se mostrará como "<7000" en la tabla.
            </flux:callout.text>
        </flux:callout>
    </flux:modal>

    <flux:heading class="mt-2" size="xl">Gestión Base de datos - Maestro</flux:heading>

    <div wire:dirty>Tienes cambios sin guardar...</div> 
    <div wire:dirty.remove>Los cambios están sincronizados.</div>
    
    <div class="flex flex-wrap items-end gap-4 mb-6 mt-4">

        <flux:dropdown position="bottom" align="end">
            <flux:button 
                icon="globe-europe-africa"
                icon:variant="outline"
                color="success">
                Añadir País
            </flux:button>

            <flux:popover class="w-[30%]">
                <form wire:submit="addColumn">
                    <flux:field>
                        <flux:label badge="Requerido">País</flux:label>
                        <flux:input.group>
                            <flux:input
                                wire:model="newColumnName"
                                placeholder="Ingrese..."
                            />
                            <flux:button type="submit" wire:target="addColumn" icon="plus">
                                Agregar
                            </flux:button>
                        </flux:input.group>
                        <flux:error name="newColumnName" />
                    </flux:field>

                    <flux:description class="mt-2 text-sm text-gray-500">
                        Escriba el nombre del país que desea agregar. No puede repetir uno existente.
                    </flux:description>
                </form>
            </flux:popover>
        </flux:dropdown>

        <flux:dropdown position="bottom" align="end">
            <flux:modal.trigger name="operand-modal">
                <flux:button icon="plus" color="success">
                    Añadir Tarifa
                </flux:button>
            </flux:modal.trigger>
        </flux:dropdown>

        {{-- Tipo de Servicio --}}
        <flux:radio.group 
            wire:model="type_service" 
            label="Seleccione un Maestro" 
            variant="pills"
            class="flex-wrap"
        >
            <flux:radio value="pu_aereo" label="Pick Up Aéreo" />
            <flux:radio value="pu_maritimo" label="Pick Up Marítimo" />
            <flux:radio value="flete_aereo" label="Flete Aéreo" />
            <flux:radio value="flete_maritimo" label="Flete Marítimo" />
        </flux:radio.group>

        {{-- Spacer para empujar botones a la derecha --}}
        <flux:spacer />

        {{-- Mostrar/Ocultar Moneda --}}
        @if($enableCurrencyFeature)
            <flux:button @click="$wire.toggleCurrencyRow()">
                <span x-show="!$wire.showCurrencyRow">Mostrar Moneda</span>
                <span x-show="$wire.showCurrencyRow">Ocultar Moneda</span>
            </flux:button>
        @endif

        {{-- Guardar Cambios --}}
        <flux:button wire:click="save" color="primary">
            Guardar Cambios
        </flux:button>

    </div>

    <div x-show="$wire.table_columns.length > 1" class="overflow-x-auto min-w-[400px]">
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

                                <flux:modal.trigger name="dichotomic-modal">
                                    <flux:tooltip content="Borrar columna" position="top">
                                        <flux:button size="xs" 
                                            @click="prepareDeletion({
                                                method: 'removeColumn',
                                                param: {{ $column['id'] }},
                                                heading: 'Borrar Columna',
                                                message: `¿Estás seguro de que quieres eliminar el país '{{ $column['label'] }}' y todas sus tarifas?`
                                            })"
                                            class="bg-red-500!  flex-shrink-0" icon="x-mark" icon:variant="outline" />
                                    </flux:tooltip>
                                </flux:modal.trigger>
                            @endif
                        </div>
                    </flux:table.column>
                @endforeach
                <flux:table.column align="center"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($rows_data as $rowIndex => $row)
                    <flux:table.row align="center">
                        @foreach($table_columns as $colIndex => $column)
                            <flux:table.cell>
                                @if($rows_data[$rowIndex][$column['name']] == $row['tier_label'] && $row['tier_label'] === 'Mínima')
                                    <flux:badge icon="minus-circle" color="sky">Tarifa {{$rows_data[$rowIndex][$column['name']]}}</flux:badge>
                                @elseif($rowIndex == 0)
                                     <flux:modal.trigger name="minimum-tariff-modal">
                                        <flux:tooltip flux:tooltip content="Oprime para configurar la Tarifa Mínima" placement="top">
                                            <flux:badge class="cursor-pointer" variant="solid" icon="currency-dollar" size="lg" color="zinc">{{$rows_data[$rowIndex][$column['name']]}}</flux:badge>
                                        </flux:tooltip>
                                    </flux:modal.trigger>
                                
                                @elseif($rows_data[$rowIndex][$column['name']] == $row['tier_label'])
                                    <flux:modal.trigger name="operand-modal">
                                        <flux:tooltip flux:tooltip content="Oprime para configurar Tarifa" placement="top">
                                            <flux:badge class="cursor-pointer" variant="pill" icon="currency-dollar"
                                                @click="parseAndSetTariff('{{ $rows_data[$rowIndex][$column['name']] }}',
                                                                            {{ $rowIndex }},
                                                                            '{{ $column['name'] }}');"
                                            >
                                            {{ str_replace(['<=', '>='], ['≤', '≥'], $rows_data[$rowIndex][$column['name']]) }}
                                            </flux:badge>
                                        </flux:tooltip>
                                    </flux:modal.trigger>
                                @else
                                    <flux:input icon="percent-badge"
                                        wire:model.defer="rows_data.{{ $rowIndex }}.{{ $column['name'] }}"
                                        readonly
                                        type="text"
                                        placeholder="{{ $column['label'] }}"
                                        />
                                @endif
                            </flux:table.cell>
                        @endforeach
                        <flux:table.cell>
                            @if($row['tier_id'] !== null)
                                <flux:modal.trigger name="dichotomic-modal">
                                    <flux:tooltip content="Borrar fila" position="top">
                                        <flux:button size="xs"
                                            @click="prepareDeletion({
                                                method: 'removeRow',
                                                param: {{ $row['tier_id'] }},
                                                heading: 'Borrar Fila',
                                                message: `¿Estás seguro de que quieres eliminar la tarifa '{{ $row['tier_label'] }}'?`
                                            })"
                                            class="bg-red-500!" icon="x-mark" icon:variant="outline" />
                                    </flux:tooltip>
                                </flux:modal.trigger>
                            @else
                                <flux:badge icon="minus-circle" color="red">Eliminar</flux:badge>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>

            {{-- Fila de Divisas (se muestra condicionalmente) --}}
            @if($enableCurrencyFeature)
                <tfoot x-show="$wire.showCurrencyRow" class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <td class="p-4 font-semibold text-center text-gray-700 dark:text-gray-200">Moneda</td>
                        @foreach($table_columns as $colIndex => $column)
                            @if($colIndex > 0)
                                <td class="p-2">
                                    <flux:select wire:model.live="service_currencies.{{ $column['id'] }}">
                                        <flux:select.option value="">Sin Moneda</flux:select.option>
                                        @foreach($currencies as $currency)
                                            <flux:select.option value="{{ $currency->id }}">{{ $currency->code }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                </td>
                            @endif
                        @endforeach
                        <td class="p-2"></td> <!-- Celda vacía para alinear con la columna de acciones -->
                    </tr>
                </tfoot>
            @endif
        </flux:table>
    </div>

    <div x-show="$wire.table_columns.length <= 1" class="text-center py-8">
        <p class="text-gray-500">No se encontraron datos para el tipo de servicio "{{ $serviceTypeName }}".</p>
        <p class="text-gray-400 text-sm mt-2">Puedes empezar añadiendo un nuevo país.</p>
    </div>
</div>
