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
                        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">● Maestro de Monedas</h2>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Crea, edita y elimina las monedas del sistema. (BASADAS EN EL DÓLAR)</p>
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
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            {{ $isEditing ? 'Editar Moneda' : 'Nueva Moneda' }}
                        </h3>
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
                                label="Valor (referencia USD)"
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
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400 sticky top-0">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">Código</th>
                                        <th scope="col" class="px-4 py-3">Nombre</th>
                                        <th scope="col" class="px-4 py-3">Valor</th>
                                        <th scope="col" class="px-4 py-3 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($currencies as $currency)
                                        <tr class="bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $currency->code }}</td>
                                            <td class="px-4 py-3">{{ $currency->name }}</td>
                                            <td class="px-4 py-3">{{ number_format($currency->value, 4) }}</td>
                                            <td class="px-4 py-3 text-center">
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
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-10 text-gray-500">
                                                <p>No hay monedas registradas.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>