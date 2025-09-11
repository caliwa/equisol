<div x-data="{
    isVisibleAssignPermissionModal: $wire.entangle('isVisibleAssignPermissionModal').live,
}"
@keydown.escape.window.prevent="closeTopModal()"
>
{{-- MARK: Article1.1--}}
@if($isVisibleAssignPermissionModal)

<div x-show="isVisibleAssignPermissionModal" 
    x-effect="
        if (isVisibleAssignPermissionModal && !modalStack.includes('isVisibleAssignPermissionModal')) {
            modalStack.push('isVisibleAssignPermissionModal');
            escapeEnabled = true; removeTabTrapListener();
        } else if (!isVisibleAssignPermissionModal) {
            modalStack = modalStack.filter(id => id !== 'isVisibleAssignPermissionModal');
            const element = document.getElementById('isVisibleCreateRolesModal');
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
<div x-show="isVisibleAssignPermissionModal" x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-90"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-90" id="isVisibleAssignPermissionModal"
    class="transform-gpu fixed overflow-x-hidden overflow-y-auto inset-0 items-center justify-center top-4 md:inset-0 h-modal sm:h-full fade-in-scale"
    style="z-index: {{$zIndexModal + 99 + 1}};">
        <div class="relative w-full h-full">
            <div class="absolute inset-0 flex justify-center items-start mt-24 pointer-events-none">
                <button class="cursor-none inline-flex items-center justify-center p-3 text-sm font-medium text-center border border-amber-800 text-white bg-gray-300 rounded hover:bg-blue-800 rounded-lg focus:ring-4 focus:ring-primary-300 sm:w-auto">
                    <div class="flex justify-center items-center">
                        <i class="fa-solid fa-magnifying-glass text-black font-bold text-4xl"></i>
                    </div>
                </button>
            </div>
    <div class="fixed inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <flux:heading size="xl">
                                ● Asignar Permisos a ROL: {{ $selectedRole['name'] }}
                            </flux:heading>

                            <!-- Permisos -->
                            <div class="mt-4 space-y-4">
                                <div>
                                    <flux:checkbox.group wire:model="selectedPermissions" label="Permisos Disponibles">
                                        @foreach($permissions as $permission)
                                            <flux:checkbox label="{{ $permission->name }}"
                                                         value="{{ $permission->id }}" />
                                        @endforeach
                                    </flux:checkbox.group>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end gap-2">
                    <flux:button wire:click="CloseModalClick('isVisibleAssignPermissionModal')"
                            x-on:click="isVisibleAssignPermissionModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Cerrar
                    </flux:button>
                    <flux:button
                        wire:click="updateRolePermissions"
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
</div>
@endif

</div>
