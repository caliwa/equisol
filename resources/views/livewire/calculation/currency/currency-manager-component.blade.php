<div x-data="{
    isVisibleCurrencyManagerComponent: $wire.entangle('isVisibleCurrencyManagerComponent').live,
}"
@if(config('modalescapeeventlistener.is_active')) @keydown.escape.window.prevent="closeTopModal()" @endif
>

    {{-- MARK: Modal --}}
    @if($isVisibleCurrencyManagerComponent)
    <div x-show="isVisibleCurrencyManagerComponent"
        x-effect="
            if (isVisibleCurrencyManagerComponent && !modalStack.includes('isVisibleCurrencyManagerComponent')) {
                modalStack.push('isVisibleCurrencyManagerComponent');
                escapeEnabled = true; removeTabTrapListener();
            } else if (!isVisibleCurrencyManagerComponent) {
                modalStack = modalStack.filter(id => id !== 'isVisibleCurrencyManagerComponent');
                const element = document.getElementById('isVisibleCurrencyManagerComponent');
                if(element){
                    element.classList.add('fade-out-scale');
                }
            }
            focusModal(modalStack[modalStack.length - 1]);
        "
        >
        <div class="fixed top-0 left-0 w-screen h-screen bg-gray-900/50 backdrop-blur-lg"
        style="z-index: {{ ($zIndexModal ?? 10) + 99 }};"></div>
    </div>

    <div x-show="isVisibleCurrencyManagerComponent"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90" id="isVisibleCurrencyManagerComponent"
        class="fixed inset-0 flex items-center justify-center overflow-x-hidden overflow-y-auto transform-gpu top-4 md:inset-0 h-modal sm:h-full fade-in-scale"
        style="z-index: {{ ($zIndexModal ?? 10) + 99 + 1 }};">
        <div class="relative w-full max-w-4xl p-4">
            <div class="p-6 bg-white dark:bg-gray-900 shadow-md rounded-lg">
                {{-- Cabecera del Modal --}}
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <flux:heading size="xl">Maestro de Monedas</flux:heading>
                        <flux:description class="mt-1">Crea, edita y elimina las monedas del sistema.</flux:description>
                    </div>
                    <flux:button icon="x-mark" variant="subtle"
                        wire:click="CloseModalClick('isVisibleCurrencyManagerComponent')"
                        x-on:click="isVisibleCurrencyManagerComponent = false"
                    />
                </div>

                {{-- Formulario y Tabla --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Columna del Formulario --}}
                    <div class="md:col-span-1">
                        <flux:heading size="lg" class="mb-4">
                            {{ $isEditing ? 'Editar Moneda' : 'Nueva Moneda' }}
                        </flux:heading>
                        <form wire:submit.prevent="save" class="space-y-4">
                            <flux:input
                                label="Código (ISO 4217)"
                                wire:model="code"
                                placeholder="Ej: USD"
                                :error="$errors->first('code')"
                                maxlength="3"
                            />
                            <flux:input
                                label="Nombre"
                                wire:model="name"
                                placeholder="Ej: US Dollar"
                                :error="$errors->first('name')"
                            />
                            <flux:input
                                type="number"
                                step="any"
                                label="Valor"
                                wire:model="value"
                                placeholder="Ej: 1.07"
                                :error="$errors->first('value')"
                            />

                            <div class="flex items-center gap-2 pt-2">
                                <flux:button type="submit" variant="primary" color="blue" class="w-full">
                                    {{ $isEditing ? 'Actualizar Moneda' : 'Guardar Moneda' }}
                                </flux:button>
                                @if($isEditing)
                                    <flux:button wire:click="create" variant="outline" class="w-full">
                                        Cancelar
                                    </flux:button>
                                @endif
                            </div>
                        </form>
                    </div>

                    {{-- Columna de la Tabla --}}
                    <div class="md:col-span-2">
                         <div class="overflow-auto rounded-lg border border-gray-200 dark:border-gray-700 max-h-96">
                            <flux:table>
                                <flux:table.columns sticky>
                                    <flux:table.column align="center">Código</flux:table.column>
                                    <flux:table.column align="center">Nombre</flux:table.column>
                                    <flux:table.column align="center">Valor</flux:table.column>
                                    <flux:table.column align="center">Acciones</flux:table.column>
                                </flux:table.columns>

                                <flux:table.rows>
                                    @forelse($currencies as $currency)
                                        <flux:table.row wire:key="currency-row-{{ $currency->id }}">
                                            <flux:table.cell align="center" class="font-medium text-gray-900 dark:text-white">
                                                {{ $currency->code }}
                                            </flux:table.cell>
                                            <flux:table.cell align="center">
                                                {{ $currency->name }}
                                            </flux:table.cell>
                                            <flux:table.cell align="center">
                                                {{ number_format($currency->value, 4) }}
                                            </flux:table.cell>
                                            <flux:table.cell align="center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <flux:button size="sm" icon="pencil-square" wire:click="edit({{ $currency->id }})" />
                                                    <flux:button size="sm" icon="trash" color="red"
                                                        @click="prepareDichotomic({
                                                            method: 'deleteCurrency',
                                                            param: {{ $currency->id }},
                                                            heading: 'Borrar Moneda',
                                                            message: `¿Estás seguro de que quieres eliminar esta moneda?`,
                                                            modalDichotomicBtnText: 'Borrar'
                                                        })"
                                                    />
                                                </div>
                                            </flux:table.cell>
                                        </flux:table.row>
                                    @empty
                                        <flux:table.row>
                                            <flux:table.cell colspan="4" class="text-center py-10 text-gray-500">
                                                <p>No hay monedas registradas.</p>
                                            </flux:table.cell>
                                        </flux:table.row>
                                    @endforelse
                                </flux:table.rows>
                            </flux:table>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>