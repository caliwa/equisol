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
    <div class="relative w-full h-full">
        <div class="p-6 bg-white dark:bg-gray-900 shadow-md rounded-lg">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">● Maestro de Tiempos de Tránsito</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Gestiona los días de tránsito para cada origen y modo de transporte.</p>
                </div>
                <flux:button icon="x-mark" variant="subtle"
                    wire:click="CloseModalClick('isVisibleIndexTransitTimeManagerComponent')"
                    x-on:click="isVisibleIndexTransitTimeManagerComponent = false"
                />
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-bold">
                                Origen
                            </th>
                            @foreach($transitModes as $mode)
                                <th scope="col" class="px-6 py-4 text-center font-bold">
                                    {{ ucfirst($mode->name) }} (días)
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($origins as $origin)
                            <tr class="bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $origin->name }}
                                </th>
                                @foreach($transitModes as $mode)
                                    <td class="px-2 py-2">
                                        @php
                                            $errorKey = 'transitData.' . $origin->id . '.' . $mode->id;
                                        @endphp

                                        <flux:input
                                            type="number"
                                            min="0"
                                            size="sm"
                                            wire:model.blur="transitData.{{ $origin->id }}.{{ $mode->id }}"
                                            placeholder="N/A"
                                            class="w-28"
                                            class:input="text-center"
                                            :error="$errors->first($errorKey)" 
                                        />
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($transitModes) + 1 }}" class="text-center py-10 text-gray-500">
                                    <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                                    <p>No se encontraron orígenes en la base de datos.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <flux:button
                    wire:click="saveTransitTimes"
                    @click="blockInteractions($event);"
                    variant="primary"
                    class="mt-2"
                    color="blue"
                >
                    Guardar Cambios
            </flux:button>
        </div>
    </div>
</div>
@endif

</div>