<div x-data="{
    isVisibleEditRegisterUserModal: $wire.entangle('isVisibleEditRegisterUserModal').live,
}"
@keydown.escape.window.prevent="closeTopModal()"
>
{{-- MARK: Article1.1--}}
@if($isVisibleEditRegisterUserModal)

<div x-show="isVisibleEditRegisterUserModal"
    x-effect="
        if (isVisibleEditRegisterUserModal && !modalStack.includes('isVisibleEditRegisterUserModal')) {
            modalStack.push('isVisibleEditRegisterUserModal');
            escapeEnabled = true; removeTabTrapListener();
        } else if (!isVisibleEditRegisterUserModal) {
            modalStack = modalStack.filter(id => id !== 'isVisibleEditRegisterUserModal');
            const element = document.getElementById('isVisibleEditRegisterUserModal');
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
<div x-show="isVisibleEditRegisterUserModal" x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-90"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-90" id="isVisibleEditRegisterUserModal"
    class="fixed inset-0 items-center justify-center overflow-x-hidden overflow-y-auto transform-gpu top-4 md:inset-0 h-modal sm:h-full fade-in-scale"
    style="z-index: {{$zIndexModal + 99 + 1}};">
        <div class="relative w-full h-full">
            <div class="absolute inset-0 flex items-start justify-center mt-24 pointer-events-none">
                <button class="inline-flex items-center justify-center p-3 text-sm font-medium text-center text-white bg-gray-300 border rounded rounded-lg cursor-none border-amber-800 hover:bg-blue-800 focus:ring-4 focus:ring-primary-300 sm:w-auto">
                    <div class="flex items-center justify-center">
                        <i class="text-4xl font-bold text-black fa-solid fa-magnifying-glass"></i>
                    </div>
                </button>
            </div>
<div class="fixed inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                        <div class="flex gap-2 items-center">
                            <flux:heading size="xl">
                                ● @if($currentUserId)Edición @endif Usuario
                            </flux:heading>
                            <flux:icon.cursor-arrow-rays />
                        </div>


                        <div class="mt-4 space-y-4">
                            <!-- Avatar -->
                            {{-- <div class="flex flex-col items-center mb-6">
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
                                            // Validar tipo de archivo
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
                            </div> --}}

                            <!-- Campos básicos en grid de 2 columnas -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <flux:label>
                                        Documento
                                    </flux:label>
                                    <flux:input type="number"
                                        @keydown.enter="handleEnter"
                                        @keydown.enter="loadingSpinner($event)"
                                        wire:keydown.enter="SaveRegisterUser"
                                        wire:model="identification"
                                        {{-- readonly --}}
                                        placeholder="Número de documento"
                                    />
                                </div>
                                
                                <div>
                                    <flux:label>
                                        Nombre de Usuario
                                    </flux:label>
                                    <flux:input type="text"
                                        @keydown.enter="handleEnter"
                                        @keydown.enter="loadingSpinner($event)"
                                        wire:keydown.enter="SaveRegisterUser"
                                        wire:model="name"
                                        oninput="this.value = this.value.toLowerCase();"
                                        placeholder="Nombre de usuario para inicio de sesión"
                                    />
                                </div>

                                <div>
                                    <flux:label>
                                        Nombre Completo
                                    </flux:label>
                                    <flux:input type="text"
                                        @keydown.enter="handleEnter"
                                        @keydown.enter="loadingSpinner($event)"
                                        wire:keydown.enter="SaveRegisterUser"
                                        wire:model="full_name"
                                        placeholder="Ingrese nombre completo"
                                    />
                                </div>

                                <div>
                                    <flux:label>
                                        Correo Electrónico
                                    </flux:label>
                                    <flux:input type="email"
                                        @keydown.enter="handleEnter"
                                        @keydown.enter="loadingSpinner($event)"
                                        wire:keydown.enter="SaveRegisterUser"
                                        wire:model="email"
                                        placeholder="correo@ejemplo.com"
                                    />
                                </div>

                                <div>
                                    <flux:label>
                                        Contraseña 
                                        @if($currentUserId)
                                            (dejar en blanco para mantener la actual)
                                        @endif
                                    </flux:label>
                                    <flux:input 
                                        type="password"
                                        @keydown.enter="handleEnter"
                                        @keydown.enter="loadingSpinner($event)"
                                        wire:keydown.enter="SaveRegisterUser"
                                        wire:model="password"
                                        placeholder="********"
                                    />
                                </div>

                            </div>

                            <div class="mt-6">

                                <div class="p-4 border border-gray-300 rounded-md bg-gray-50">
                                     <flux:checkbox.group label="Roles del usuario" variant="cards" class="grid grid-cols-3 gap-4">
                                        @foreach($availableRoles as $role)

                                            <flux:checkbox
                                                value="{{ $role->id }}"
                                                label="{{ $role->name }}"
                                                wire:click="toggleRole({{ $role->id }})"
                                                :checked="in_array($role->id, $selectedRoles)"
                                                description="{{ $role->description }}"
                                            />
                                        @endforeach
                                    </flux:checkbox.group>
                                </div>
                                
                                <!-- Mostrar roles seleccionados -->
                                @if(!empty($selectedRoles))
                                    <div class="mt-3">
                                        <flux:heading size="sm">
                                            Roles seleccionados:
                                        </flux:heading>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($availableRoles->whereIn('id', $selectedRoles) as $selectedRole)
                                                <flux:badge size="sm" color="blue">
                                                    {{ $selectedRole->name }}
                                                    <button 
                                                        type="button" 
                                                        wire:click="toggleRole({{ $selectedRole->id }})"
                                                        class="ml-1 text-blue-600 hover:text-blue-800"
                                                    >
                                                    </button>
                                                </flux:badge>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Mostrar todos los permisos únicos de los roles seleccionados -->
                            @if(!empty($selectedRoles))
                                @php
                                    $allPermissions = collect();
                                    foreach($availableRoles->whereIn('id', $selectedRoles) as $role) {
                                        $allPermissions = $allPermissions->merge($role->permissions);
                                    }
                                    $uniquePermissions = $allPermissions->unique('name')->pluck('name');
                                @endphp
                                
                                @if($uniquePermissions->count() > 0)
                                    <div class="mt-4">
                                        <flux:heading size="sm">
                                            Permisos totales del usuario:
                                        </flux:heading>
                                        <div class="p-3 bg-green-50 rounded-md border border-green-200 max-h-32 overflow-y-auto">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($uniquePermissions as $permission)
                                                    <flux:badge size="sm" color="green">
                                                        {{ $permission }}
                                                    </flux:badge>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end gap-2">
                <flux:button wire:click="CloseModalClick('isVisibleEditRegisterUserModal')"
                        x-on:click="isVisibleEditRegisterUserModal = false"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                    Cerrar
                </flux:button>
                <flux:button
                    wire:click="SaveRegisterUser"
                    @click="loadingSpinner($event)"
                    variant="primary"
                    color="blue"
                >
                    Guardar
                </flux:button>
            </div>
        </div>
    </div>
</div>

</div>
</div>
@endif
</div>