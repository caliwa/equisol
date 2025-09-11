<div x-data="{
    isVisibleEditPublicRegisterUserModal: $wire.entangle('isVisibleEditPublicRegisterUserModal').live,
}"
@keydown.escape.window.prevent="closeTopModal()"
>
{{-- MARK: Article1.1--}}
@if($isVisibleEditPublicRegisterUserModal)
<div x-show="isVisibleEditPublicRegisterUserModal"
    x-effect="
        if (isVisibleEditPublicRegisterUserModal && !modalStack.includes('isVisibleEditPublicRegisterUserModal')) {
            modalStack.push('isVisibleEditPublicRegisterUserModal');
            escapeEnabled = true; removeTabTrapListener();
        } else if (!isVisibleEditPublicRegisterUserModal) {
            modalStack = modalStack.filter(id => id !== 'isVisibleEditPublicRegisterUserModal');
            const element = document.getElementById('isVisibleEditPublicRegisterUserModal');
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
<div x-show="isVisibleEditPublicRegisterUserModal" x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-90"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-90" id="isVisibleEditPublicRegisterUserModal"
    class="fixed inset-0 items-center justify-center overflow-x-hidden overflow-y-auto transform-gpu top-4 md:inset-0 h-modal sm:h-full fade-in-scale"
    style="z-index: {{$zIndexModal + 1 + 99}};">
        <div class="relative w-full h-full">
            <div class="absolute inset-0 flex items-start justify-center mt-24 pointer-events-none">
                <button class="inline-flex items-center justify-center p-3 text-sm font-medium text-center text-white bg-gray-300 border rounded rounded-lg cursor-none border-amber-800 hover:bg-blue-800 focus:ring-4 focus:ring-primary-300 sm:w-auto">
                    <div class="flex items-center justify-center">
                        <i class="text-4xl font-bold text-black fa-solid fa-magnifying-glass"></i>
                    </div>
                </button>
            </div>
<div class="fixed inset-0 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 uppercase">
                                ● Editar Mi Usuario
                                <i class="text-sm cursor-pointer fas fa-user-cog text-muted-foreground"></i>
                            </h3>

                        <div class="mt-4 space-y-4">
                            <!-- Avatar -->
                            <div class="flex flex-col items-center mb-6">
                                @if ($avatar)
                                    <img src="{{ $avatar->temporaryUrl() }}"
                                        style="width: 90px; height: 90px; object-fit: cover; border: 2px solid black; border-radius: 50%;">
                                @elseif ($currentAvatar)
                                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(storage_path('app/public/' . $currentAvatar))) }}"
                                        style="width: 90px; height: 90px; object-fit: cover; border: 2px solid black; border-radius: 50%;">
                                @else
                                <div class="flex items-center justify-center w-24 h-24 mb-2 bg-gray-200 rounded-full">
                                        <i class="text-3xl text-gray-400 fas fa-user"></i>
                                    </div>
                                @endif

                                <label class="px-4 py-2 transition-colors rounded-md cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <span class="text-sm text-gray-600">Seleccionar Avatar</span>
                                    <input type="file" wire:model="avatar" class="hidden" accept=".jpeg,.jpg,.png"
                                        onchange="const file = this.files[0];
                                        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                                        const maxSize = 1024 * 1024; // 1MB en bytes
                                        if (file) {
                                            if (!validTypes.includes(file.type)) {
                                                Livewire.dispatch('confirm-validation-modal', ['Por favor, selecciona un archivo de imagen (.jpeg, .jpg, .png).']);
                                                this.value = '';
                                                return;
                                            }

                                            // Validar tamaño del archivo
                                            if (file.size > maxSize) {
                                                Livewire.dispatch('confirm-validation-modal', ['El archivo es demasiado grande. El tamaño máximo permitido es 1MB.']);
                                                this.value = ''; // Limpiar el archivo
                                            }
                                        }">
                                            </label>
                                        </div>

                                        <!-- Campos básicos -->
                                        <div>
                                            <label class="block mb-1 text-sm font-medium text-gray-700">Documento</label>
                                            <input type="text"
                                            wire:model="identification"
                                            readonly
                                            class="w-full px-3 py-2 text-gray-500 bg-gray-100 border rounded-md" placeholder="Número de documento">
                                        </div>
                                        <div>
                                            <label class="block mb-1 text-sm font-medium text-gray-700">Nombre
                                                Completo</label>
                                            <input type="text" @keydown.enter="handleEnter"
                                                @keydown.enter="loadingSpinner($event)" wire:keydown.enter="SaveRegisterUser"
                                                wire:model="full_name" class="w-full px-3 py-2 border @if($errors->has("
                                                full_name")) bg-red-100 border-red-500 @else border-gray-300 @endif
                                                rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                                outline-none transition-colors" placeholder="Ingrese nombre completo">
                                        </div>

                                        <div>
                                            <label class="block mb-1 text-sm font-medium text-gray-700">Correo
                                                Electrónico</label>
                                            <input type="email" @keydown.enter="handleEnter"
                                                @keydown.enter="loadingSpinner($event)" wire:keydown.enter="SaveRegisterUser"
                                                wire:model="email" class="w-full px-3 py-2 border @if($errors->has("
                                                email")) bg-red-100 border-red-500 @else border-gray-300 @endif
                                                rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                                outline-none transition-colors" placeholder="correo@ejemplo.com">
                                        </div>

                                        <div>
                                            <label class="block mb-1 text-sm font-medium text-gray-700">
                                                Contraseña (dejar en blanco para mantener la actual)
                                            </label>
                                            <input type="password" @keydown.enter="handleEnter"
                                                @keydown.enter="loadingSpinner($event)" wire:keydown.enter="SaveRegisterUser"
                                                wire:model="password" class="w-full px-3 py-2 border @if($errors->has("
                                                password")) bg-red-100 border-red-500 @else border-gray-300 @endif
                                                rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                                outline-none transition-colors" placeholder="********">
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button @click="loadingSpinner($event)" wire:click="SaveRegisterUser"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center justify-center w-full px-4 py-2 text-base font-medium text-white transition-colors bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                <span>Guardar</span>

                                <svg wire:loading wire:target="SaveRegisterUser"
                                    class="w-5 h-5 ml-2 text-white animate-spin" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </button>

                            <button wire:click="$set('isVisibleEditPublicRegisterUserModal', false)"
                                x-on:click="isVisibleEditPublicRegisterUserModal = false" type="button"
                                class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
