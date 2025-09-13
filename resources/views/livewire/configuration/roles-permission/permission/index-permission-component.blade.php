<div class="p-6">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="/configuracion" icon="cog-6-tooth"/>
        <flux:breadcrumbs.item href="/configuracion">Configuración</flux:breadcrumbs.item>
        <flux:breadcrumbs.item icon="document-currency-euro"/>
        <flux:breadcrumbs.item>Permisos</flux:breadcrumbs.item>
    </flux:breadcrumbs>

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

        <div class="mt-4">
            <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Buscar permisos..." />
        </div>
        
        <div class="mt-6 overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>ID</flux:table.column>
                    <flux:table.column>Nombre</flux:table.column>
                    <flux:table.column>Descripción</flux:table.column>
                    <flux:table.column>Roles</flux:table.column>
                    <flux:table.column>Acciones</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($permissions as $permission)
                        <flux:table.row wire:key="permission-row-{{ $permission->id }}">
                            <flux:table.cell class="text-gray-900">
                                {{ $permission->id }}
                            </flux:table.cell>
                            <flux:table.cell class="font-medium text-gray-900">
                                {{ $permission->name }}
                            </flux:table.cell>
                            <flux:table.cell class="text-gray-500">
                                {{ $permission->description }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($permission->roles as $role)
                                        <flux:badge size="sm" variant="solid" color="green">{{ $role->name }}</flux:badge>
                                    @empty
                                        <flux:badge size="sm" variant="pill" color="zinc">No posee rol</flux:badge>
                                    @endforelse
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center space-x-3">
                                    <flux:tooltip content="Editar Permiso" position="top">
                                        <flux:button @click="loadingSpinner($event)"
                                            wire:click="OpenEditPermission({{ $permission->id }})"
                                            icon:trailing="pencil-square"
                                            icon:variant="outline"
                                            class="text-blue-600!"
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
                                            icon:trailing="trash"
                                            icon:variant="outline"
                                            class="text-red-600!"
                                            >
                                        </flux:button>
                                    </flux:tooltip>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5">
                                <flux:description class="text-center py-4">
                                    No hay permisos registrados.
                                </flux:description>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <div class="mt-4">
            {{ $permissions->links() }}
        </div>
    </div>
</div>