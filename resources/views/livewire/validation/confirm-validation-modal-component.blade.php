<div x-data="{
        isVisibleConfirmValidationModal: $wire.entangle('isVisibleConfirmValidationModal').live,
    }"
    @if(config('modalescapeeventlistener.is_active')) @keydown.escape.window.prevent="closeTopModal()" @endif
>
    @if($isVisibleConfirmValidationModal)

    <div x-show="isVisibleConfirmValidationModal" 
        x-effect="
            if (isVisibleConfirmValidationModal && !modalStack.includes('isVisibleConfirmValidationModal')) {
                modalStack.push('isVisibleConfirmValidationModal');
                if(checkInterval){
                    clearInterval(checkInterval);
                }
                escapeEnabled = true;
            } else if (!isVisibleConfirmValidationModal) {
                modalStack = modalStack.filter(id => id !== 'isVisibleConfirmValidationModal');
                document.getElementById('isVisibleConfirmValidationModal').classList.add('fade-out-scale');
            }
            focusModal(modalStack[modalStack.length - 1]);
        "
        >
        <div class="fixed top-0 left-0 w-screen h-screen bg-gray-900/20 backdrop-blur-xs"
        style="z-index: 2147483645;"></div>
    </div>
    {{-- <div class="bg-white dark:bg-gray-800"> --}}
        <div x-show="isVisibleConfirmValidationModal"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90" id="isVisibleConfirmValidationModal"
        class="transform-gpu fixed w-full h-full flex items-center justify-center h-modal top-0 md:inset-0 sm:h-full fade-in-scale"
        style="z-index: 2147483646;">
            <div class="absolute inset-0 flex justify-center items-center pointer-events-none">
                <div class="animate-ping w-32 h-32 flex justify-center items-center">
                    <i class="text-yellow-500 fa-solid fa-circle-exclamation text-5xl"></i>
                </div>
            </div>
            <div class="relative inline-block px-4 pt-10">
                <div class="bg-white rounded-lg shadow-sm dark:bg-gray-800 overflow-y-auto m-auto">
                    <div class="flex items-start justify-between border-b border-gray-200 rounded-t dark:border-gray-700">
                        <div class="ml-[45%] flex items-center justify-center mx-auto w-16 h-16">
                            <i class="text-yellow-500 fa-solid fa-circle-exclamation text-5xl"></i>
                        </div>
                        <button 
                        wire:click="CloseModalClick('isVisibleConfirmValidationModal')" 
                        x-on:click="isVisibleConfirmValidationModal = false"
                        wire:loading.attr="disabled" wire:loading.class.remove="bg-transparent" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    <div class="shadow-xl border-b-white border-r-white border-l-white dark:border-black px-6 py-4 space-y-4 space-x-6 rounded-b-lg min-[1139px]:mx-2 min-[1139px]:mb-2">
                        <div class="mb-4 mt-3">
                            <p class="font-bold dark:text-white">• ATENCIÓN</p>
                        </div>
                        <p class="dark:text-white">{{$errorMsgConfirmationModal}}</p>
                        <div class="flex justify-center items-center">
                            <button 
                            wire:click="CloseModalClick('isVisibleConfirmValidationModal')" 
                            x-on:click="isVisibleConfirmValidationModal = false"
                            wire:loading.attr="disabled" wire:loading.class.remove="bg-blue-900" class="mb-2 inline-flex px-3 py-2 text-sm font-medium text-center text-white bg-blue-900 border border-gray-300 rounded-lg hover:bg-black focus:ring-4 focus:ring-primary-300 sm:w-auto dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                Continuar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {{-- </div> --}}
    @endif
</div>
