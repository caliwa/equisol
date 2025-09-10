<div class="p-6">

    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="#" icon="cube-transparent" />
        <flux:breadcrumbs.item wire:click="BackToMastersView" href="#">Maestros</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Fletes Courier</flux:breadcrumbs.item>
    </flux:breadcrumbs>
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Gestión de Proveedores de Tarifas</flux:heading>
        <flux:button wire:click="BackToMastersView" icon="arrow-left">Volver</flux:button>
    </div>

    <div class="mb-2" wire:dirty>Esperando sincronización...</div> 
    <div class="mb-2" wire:dirty.remove>Los cambios están sincronizados.</div>

    <div class="p-6 space-y-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
        <flux:heading size="md">Añadir Nuevo Proveedor</flux:heading>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <flux:field>
                <flux:label>Nombre del Proveedor (ej. FedEx)</flux:label>
                <flux:input type="text" wire:model="name" placeholder="Ingrese el nombre" />
            </flux:field>
            
            <flux:field>
                <flux:label>Código Único (ej. fedex)</flux:label>
                <flux:input type="text" wire:model="code" placeholder="Ingrese el código" />
            </flux:field>

            <flux:button wire:click="addProvider" @click="blockInteractions($event)" variant="primary" icon="plus">
                Añadir Proveedor
            </flux:button>
        </div>
    </div>

    <div>
        <flux:badge variant="pill" class="mt-4 mb-2" icon="clipboard-document-list" color="sky">Proveedores Existentes</flux:badge>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Nombre</flux:table.column>
                <flux:table.column>Código</flux:table.column>
                <flux:table.column align="center">Acciones</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($providers as $provider)
                    <flux:table.row wire:key="provider-{{ $provider->id }}">
                        <flux:table.cell class="font-medium">
                            {{ $provider->name }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="secondary">{{ $provider->code }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell align="center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('providers.edit', $provider->id) }}">
                                    <flux:button variant="primary" color="green" size="sm">
                                        Editar Zonas y Tarifas
                                    </flux:button>
                                </a>
                                <flux:button
                                    @click="prepareDichotomic({
                                        method: 'deleteProvider',
                                        param: {{ $provider->id }},
                                        heading: 'Borrar Proveedor',
                                        message: `¿Estás seguro de eliminar el proveedor '{{ $provider->name }}' y todos sus datos?`,
                                        modalDichotomicBtnText: 'Borrar'
                                    })"
                                    variant="primary" color="red" size="sm">
                                    Eliminar
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="3">
                            <div class="text-center py-8">
                                <p class="text-gray-500 dark:text-gray-400">No hay proveedores creados.</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Puedes empezar añadiendo uno nuevo desde el formulario de arriba.</p>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>