<div class="p-6">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="/configuracion" icon="cog-6-tooth"/>
        <flux:breadcrumbs.item href="/configuracion">Configuración</flux:breadcrumbs.item>
        <flux:breadcrumbs.item icon="document-currency-dollar"/>
        <flux:breadcrumbs.item>Roles</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
            <flux:heading size="xl">Gestión de Roles</flux:heading>
            <flux:button 
                @click="loadingSpinner($event)"
                wire:click="OpenCreateRolesModal"
                icon="plus"
                variant="primary"
                color="blue"
            >
                Nuevo Rol
            </flux:button>
        </div>

        <!-- Search -->
        <div class="mt-4">
            <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Buscar roles..." />
        </div>

        <!-- Roles Table -->
        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permisos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($roles as $role)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $role->id }}</td>
                            <td class="px-6 py-4">{{ $role->name }}</td>
                            <td class="px-6 py-4">{{ $role->description }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @forelse($role->permissions as $permission)
                                        <flux:badge size="sm" variant="solid" color="blue"> {{ $permission->name }}</flux:badge>
                                    @empty
                                        <flux:badge size="sm" variant="pill" color="zinc">No posee permiso/s</flux:badge>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <flux:tooltip content="Asignar Permisos" position="top">
                                        <flux:button 
                                            @click="loadingSpinner($event)"
                                            wire:click="OpenAssignPermissionToRolesModal({{ $role->id }})"
                                            icon:trailing="key"
                                            icon:variant="outline"
                                            class="text-green-600!" 
                                        >
                                        </flux:button>
                                    </flux:tooltip>
                                    
                                    <flux:tooltip content="Editar Rol" position="top">
                                        <flux:button 
                                            @click="loadingSpinner($event)"
                                            wire:click="OpenEditRolesModal({{ $role->id }})"
                                            icon:trailing="pencil-square"
                                            icon:variant="outline"
                                            class="text-blue-600!"
                                        >
                                        </flux:button>
                                    </flux:tooltip>

                                    @unless (\Illuminate\Support\Str::startsWith($role->name, 'admin-'))
                                        <flux:tooltip content="Duplicar Rol" position="top">
                                            <flux:button
                                                @click="loadingSpinner($event)"
                                                wire:click="DuplicatRol({{ $role->id }})" 
                                                icon:trailing="document-duplicate"
                                                icon:variant="outline"
                                                class="text-purple-600!"
                                            >
                                            </flux:button>
                                        </flux:tooltip>
                                    @endunless

                                    <flux:tooltip content="Eliminar Rol" position="top">
                                        <flux:button 
                                            @click="prepareDichotomic({
                                                method: 'DeleteRol',
                                                param: {{ $role->id }},
                                                heading: 'Borrar Rol',
                                                message: `¿Estás seguro de que quieres eliminar el rol '{{ $role->name }}'?`,
                                                modalDichotomicBtnText: 'Borrar'
                                            })"
                                            icon:trailing="trash"
                                            icon:variant="outline"
                                            class="text-red-600!"
                                        >
                                        </flux:button>
                                    </flux:tooltip>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="hover:bg-gray-50">
                            <td colspan="5" >
                                <flux:description class="text-center">
                                    No hay roles registrados.
                                </flux:description>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $roles->links() }}
        </div>
    </div>

</div>