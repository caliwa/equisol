<div class="p-6">
    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="/configuracion" icon="cog-6-tooth"/>
        <flux:breadcrumbs.item href="/configuracion">Configuración</flux:breadcrumbs.item>
        <flux:breadcrumbs.item icon="users"/>
        <flux:breadcrumbs.item>Usuarios</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <div class="p-6 mb-6 bg-white rounded-lg shadow-md">
        <div class="flex items-center justify-between pr-5 mb-2">
            <flux:heading size="xl">Usuarios Registrados</flux:heading>
            <flux:button
                @click="loadingSpinner($event)"
                wire:click="OpenRegisterUserModal"
                icon="plus"
                variant="primary"
                color="blue"
            >
                Nuevo Usuario
            </flux:button>
        </div>

        <div class="mt-4">
            <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Buscar usuarios..." />
        </div>

        <div class="mt-8 overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>ID</flux:table.column>
                    <flux:table.column>Usuario</flux:table.column>
                    <flux:table.column>Nombre Completo</flux:table.column>
                    <flux:table.column>Correo</flux:table.column>
                    <flux:table.column>Roles Asignados</flux:table.column>
                    <flux:table.column>Permisos Totales</flux:table.column>
                    <flux:table.column align="center">Acciones</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($users as $user)
                        <flux:table.row wire:key="user-row-{{ $user->id }}">
                            <flux:table.cell>{{ $user->id }}</flux:table.cell>
                            <flux:table.cell>{{ $user->name }}</flux:table.cell>
                            <flux:table.cell>{{ $user->full_name ?? 'N/A' }}</flux:table.cell>
                            <flux:table.cell>{{ $user->email }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-wrap gap-2 max-h-[100px] overflow-y-auto">
                                    @forelse($user->roles as $role)
                                        <flux:badge size="sm" variant="solid" color="green"> {{ $role->name }}</flux:badge>
                                    @empty
                                        <flux:badge size="sm" variant="pill" color="zinc">Sin roles asignados</flux:badge>
                                    @endforelse
                                </div>
                                @if($user->roles->count() > 3)
                                    <div class="mt-1">
                                        <span class="text-xs text-gray-500">
                                            +{{ $user->roles->count() - 3 }} más
                                        </span>
                                    </div>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-wrap gap-2 max-h-[100px] overflow-y-auto">
                                    @php
                                        $allPermissions = collect();
                                        foreach($user->roles as $role) {
                                            $allPermissions = $allPermissions->merge($role->permissions);
                                        }
                                        $uniquePermissions = $allPermissions->unique('name');
                                    @endphp

                                    @forelse($uniquePermissions->take(5) as $permission)
                                        <flux:badge size="sm" variant="solid" color="blue"> {{ $permission->name }}</flux:badge>
                                    @empty
                                        <flux:badge size="sm" variant="pill" color="zinc">No hay permisos asignados</flux:badge>
                                    @endforelse

                                    @if($uniquePermissions->count() > 5)
                                        <flux:badge size="sm" variant="solid" color="blue"> +{{ $uniquePermissions->count() - 5 }} más</flux:badge>
                                    @endif
                                </div>
                            </flux:table.cell>
                            <flux:table.cell align="right">
                                <div class="flex justify-end gap-2">
                                    <flux:button
                                        @click="loadingSpinner($event)"
                                        variant="primary"
                                        color="green"
                                        wire:click="OpenEditUserModal({{ $user->id }})"
                                    >
                                        Editar
                                    </flux:button>

                                    <flux:button
                                       variant="danger"
                                        @click="prepareDichotomic({
                                            method: 'deleteUser',
                                            param: {{ $user->id }},
                                            heading: 'Borrar Usuario',
                                            message: `¿Estás seguro de que quieres eliminar el usuario '{{ $user->name }}'?`,
                                            modalDichotomicBtnText: 'Borrar'
                                        })"
                                    >
                                        Eliminar
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                        @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7" class="text-center py-4">
                                No hay usuarios registrados
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        @if($users)
        <div class="mt-4">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>