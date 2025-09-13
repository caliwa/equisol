<div x-data="{
    isVisibleIndexTransitTimeManagerComponent: $wire.entangle('isVisibleIndexTransitTimeManagerComponent').live,
}"
@if(config('modalescapeeventlistener.is_active')) @keydown.escape.window.prevent="closeTopModal()" @endif
>
{{-- MARK: Modal --}}
@if($isVisibleIndexTransitTimeManagerComponent)
<div x-show="isVisibleIndexTransitTimeManagerComponent"
    x-effect="
        if (isVisibleIndexTransitTimeManagerComponent && !modalStack.includes('isVisibleIndexTransitTimeManagerComponent')) {
            modalStack.push('isVisibleIndexTransitTimeManagerComponent');
            escapeEnabled = true; removeTabTrapListener();
        } else if (!isVisibleIndexTransitTimeManagerComponent) {
            modalStack = modalStack.filter(id => id !== 'isVisibleIndexTransitTimeManagerComponent');
            const element = document.getElementById('isVisibleIndexTransitTimeManagerComponent');
            if(element){
                element.classList.add('fade-out-scale');
            }
        }
        focusModal(modalStack[modalStack.length - 1]);
    "
    >
    <div class="fixed top-0 left-0 w-screen h-screen bg-gray-900/50 backdrop-blur-lg"
    style="z-index: {{$zIndexModal + 99}};"></div>
</div>
<div x-show="isVisibleIndexTransitTimeManagerComponent"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-90" id="isVisibleIndexTransitTimeManagerComponent"
    class="fixed inset-0 items-center justify-center overflow-x-hidden overflow-y-auto transform-gpu top-4 md:inset-0 h-modal sm:h-full fade-in-scale"
    style="z-index: {{$zIndexModal + 99 + 1}};">
    <div class="relative w-full h-full max-w-4xl mx-auto"> {{-- Se ajustó el ancho para mejor visualización --}}
        <div class="p-6 bg-white dark:bg-gray-900 shadow-md rounded-lg">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
                <div>
                    <flux:heading size="xl">Maestro de Tiempos de Tránsito</flux:heading>
                    <flux:description class="mt-1">Gestiona los días de tránsito para cada origen y modo de transporte.</flux:description>
                </div>
                <flux:button icon="x-mark" variant="subtle"
                    wire:click="CloseModalClick('isVisibleIndexTransitTimeManagerComponent')"
                    x-on:click="isVisibleIndexTransitTimeManagerComponent = false"
                />
            </div>

            <div class="w-full overflow-x-auto">
                <div class="min-w-max">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Origen</flux:table.column>
                            @foreach($transitModes as $mode)
                                <flux:table.column align="center">
                                    {{ ucfirst($mode->name) }} (días)
                                </flux:table.column>
                            @endforeach
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse($origins as $origin)
                                <flux:table.row wire:key="transit-row-{{ $origin->id }}">
                                    <flux:table.cell class="font-medium text-gray-900 dark:text-white">
                                        {{ $origin->name }}
                                    </flux:table.cell>
                                    @foreach($transitModes as $mode)
                                        <flux:table.cell align="center" wire:key="transit-cell-{{ $origin->id }}-{{ $mode->id }}">
                                            @php
                                                $errorKey = 'transitData.' . $origin->id . '.' . $mode->id;
                                            @endphp
                                            <flux:input
                                                type="number"
                                                min="0"
                                                size="sm"
                                                wire:model.blur="transitData.{{ $origin->id }}.{{ $mode->id }}"
                                                placeholder="N/A"
                                                class="w-28 mx-auto" {{-- Centrado con mx-auto --}}
                                                class:input="text-center"
                                                :error="$errors->first($errorKey)"
                                            />
                                        </flux:table.cell>
                                    @endforeach
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="{{ count($transitModes) + 1 }}" class="text-center py-10">
                                        <p class="text-gray-500">No se encontraron orígenes en la base de datos.</p>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </div>
            </div>

            <div class="flex justify-end mt-6"> {{-- Contenedor para alinear el botón a la derecha --}}
                <flux:button
                        wire:click="saveTransitTimes"
                        @click="blockInteractions($event);"
                        variant="primary"
                        color="blue"
                    >
                        Guardar Cambios
                </flux:button>
            </div>
        </div>
    </div>
</div>
@endif

</div>