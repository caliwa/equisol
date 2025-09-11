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
        
        <!-- Search -->
        <div class="mt-4">
            <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Buscar usuarios..." />
        </div>
    
        <!-- Tabla de Usuarios -->
        <div class="mt-8 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Usuario</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nombre Completo</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Correo</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Roles Asignados</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Permisos Totales</th>
                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            {{-- <td class="px-6 py-4 whitespace-nowrap"> --}}
                                {{-- @if($user->avatar)
                                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(storage_path('app/public/' . $user->avatar))) }}"
                                    alt="{{ $user->name }}" class="object-cover w-10 h-10 rounded-full">
                                @else
                                    <div class="flex items-center justify-center w-10 h-10 bg-gray-200 rounded-full">
                                        <i class="text-gray-400 fas fa-user"></i>
                                    </div>
                                @endif --}}
                            {{-- </td> --}}
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->id }}</td>

                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->full_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                            {{-- <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->cargo)
                                    <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                        {{ $user->cargo->nombre_cargo }}
                                    </span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">Sin cargo asignado</span>
                                @endif
                            </td> --}}
                            <td class="px-6 py-4 max-h-[120px] overflow-hidden">
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
                            </td>
                            
                            <td class="px-6 py-4 max-h-[120px] overflow-hidden">
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
                            </td>
                            
                            <td class="px-6 py-4 text-right">
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
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center">
                                No hay usuarios registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
        </div>
        
        <!-- Pagination -->
        @if($users)
        <div class="mt-4">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>