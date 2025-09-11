<div class="p-6">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="/configuracion" icon="cog-6-tooth"/>
        <flux:breadcrumbs.item href="/configuracion">Configuración</flux:breadcrumbs.item>
        <flux:breadcrumbs.item icon="document-currency-euro"/>
        <flux:breadcrumbs.item>Permisos</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
            <flux:heading size="xl">Gestión de Permisos</flux:heading>
            <flux:button 
                @click="loadingSpinner($event)"
                wire:click="OpenCreatePermission"
                icon="plus"
                variant="primary"
                color="blue"
            >
                Nuevo Permiso
            </flux:button>
        </div>

        <!-- Search -->
        <div class="mt-4">
            <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Buscar permisos..." />
        </div>
        <!-- Permissions Table -->
        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Roles</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($permissions as $permission)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $permission->id }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $permission->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $permission->description }}</td>
                            <td class="">
                                <div class="flex flex-wrap gap-2">
                                    @forelse($permission->roles as $role)
                                        <flux:badge size="sm" variant="solid" color="green">{{ $role->name }}</flux:badge>
                                    @empty
                                        <flux:badge size="sm" variant="pill" color="zinc">No posee rol</flux:badge>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">

                                    <flux:tooltip content="Editar Permiso" position="top">
                                        <flux:button @click="loadingSpinner($event)"
                                            wire:click="OpenEditPermission({{ $permission->id }})"
                                            class="text-blue-600!"
                                            icon:trailing="pencil-square"
                                            icon:variant="outline"
                                            >
                                        </flux:button>
                                    </flux:tooltip>

                                    <flux:tooltip content="Eliminar Permiso" position="top">
                                        <flux:button @click="prepareDichotomic({
                                                method: 'DeletePermission',
                                                param: {{ $permission->id }},
                                                heading: 'Borrar Permiso',
                                                message: `¿Estás seguro de que quieres eliminar el permiso '{{ $permission->name }}'?`,
                                                modalDichotomicBtnText: 'Borrar'
                                            })"
                                            {{-- wire:click="OpenDeletePermissionDichotomic({{ $permission->id }}, '{{$permission->name}}')" --}}
                                            class="text-red-600!"
                                            icon:trailing="trash"
                                            icon:variant="outline"
                                            >
                                        </flux:button>
                                    </flux:tooltip>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="hover:bg-gray-50">
                            <th colspan="5"
                            >
                            <flux:description class="text-center">
                                No hay permisos registrados.
                            </flux:description>
                            </th>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $permissions->links() }}
        </div>
    </div>
</div>