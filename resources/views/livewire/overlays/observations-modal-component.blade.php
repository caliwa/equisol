<div x-data="{
        isVisibleObservationsModal: $wire.entangle('isVisibleObservationsModal').live,
    }"
    @if(config('modalescapeeventlistener.is_active')) @keydown.escape.window.prevent="closeTopModal()" @endif
    >
    @if($isVisibleObservationsModal)

    <div x-show="isVisibleObservationsModal" 
            x-effect="
            if (isVisibleObservationsModal && !modalStack.includes('isVisibleObservationsModal')) {
                modalStack.push('isVisibleObservationsModal');
                escapeEnabled = true; removeTabTrapListener();
            } else if (!isVisibleObservationsModal) {
                modalStack = modalStack.filter(id => id !== 'isVisibleObservationsModal');
                const element = document.getElementById('isVisibleObservationsModal');
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
        
        <div x-show="isVisibleObservationsModal"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90" id="isVisibleObservationsModal"
        class="transform-gpu fixed overflow-x-hidden overflow-y-auto inset-0 items-center justify-center top-4 md:inset-0 h-modal sm:h-full fade-in-scale"
        style="z-index: {{$zIndexModal + 99 + 1}};">
        <div class="relative max-[639px]:w-full min-[640px]:w-[90%] min-[640px]:px-4 mx-auto pt-2">
            
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-800 overflow-y-auto m-auto">
                
                <div class="flex items-center justify-between p-5 border-b border-gray-200 rounded-t dark:border-gray-700">
                    <div class="flex items-center space-x-4">
                        <flux:heading size="xl">
                            {{$title_presentation}}
                        </flux:heading>
                    </div>
                    <flux:button icon="x-mark" variant="subtle"
                        wire:click="CloseModalClick('isVisibleObservationsModal')"
                        x-on:click="isVisibleObservationsModal = false"
                    />
                </div>

                <div class="p-6 space-y-6">
                    <div class="block w-full">
                        <flux:textarea
                        x-on:keydown.enter="if ($event.target.selectionStart === $event.target.selectionEnd) { $event.target.value = $event.target.value.substring(0, $event.target.selectionStart) + ' ' + $event.target.value.substring($event.target.selectionStart); }"
                        x-on:input="this.value = this.value.replace(/\n/g, ' \n');"
                        wire:model="observations_data" name="observations_data" id="observations_data" rows="4" 
                        class="block p-2.5 w-full text-sm rounded-lg " placeholder="Observaciones">
                        </flux:textarea>
                    </div>
                </div>
                <flux:button 
                    class="m-4"
                    @click="loadingSpinner($event);"
                    wire:click="save"
                    variant="primary"
                    color="blue"
                    >
                    Guardar
                </flux:button>

            </div>
        </div>
    </div>
    @endif

</div>