<div class="p-6">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="/configuracion" icon="cog-6-tooth"/>
        <flux:breadcrumbs.item href="/configuracion">Configuración</flux:breadcrumbs.item>
        <flux:breadcrumbs.item icon="document-currency-dollar"/>
        <flux:breadcrumbs.item>Roles</flux:breadcrumbs.item>
    </flux:breadcrumbs>

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

        <div class="mt-4">
            <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Buscar roles..." />
        </div>

        <div class="mt-6 overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>ID</flux:table.column>
                    <flux:table.column>Nombre</flux:table.column>
                    <flux:table.column>Descripción</flux:table.column>
                    <flux:table.column>Permisos</flux:table.column>
                    <flux:table.column>Acciones</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($roles as $role)
                        <flux:table.row wire:key="role-row-{{ $role->id }}">
                            <flux:table.cell>{{ $role->id }}</flux:table.cell>
                            <flux:table.cell>{{ $role->name }}</flux:table.cell>
                            <flux:table.cell>{{ $role->description }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($role->permissions as $permission)
                                        <flux:badge size="sm" variant="solid" color="blue"> {{ $permission->name }}</flux:badge>
                                    @empty
                                        <flux:badge size="sm" variant="pill" color="zinc">No posee permiso/s</flux:badge>
                                    @endforelse
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
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
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5">
                                <flux:description class="text-center py-4">
                                    No hay roles registrados.
                                </flux:description>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <div class="mt-4">
            {{ $roles->links() }}
        </div>
    </div>

</div>