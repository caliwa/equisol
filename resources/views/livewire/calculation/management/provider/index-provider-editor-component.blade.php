<div class="p-6">
    <a href="{{ route('providers.index') }}" class="text-blue-600 hover:underline">&larr; Volver a la lista de proveedores</a>

    <flux:heading size="xl">Gestionando: <span class="text-indigo-600">{{ $provider->name }}</span></flux:heading>
    
    {{-- Invocamos el componente para gestionar las ZONAS de este proveedor --}}
    <div class="mt-8">
        <livewire:calculation.management.provider.components.zone-manager-component
        :provider="$provider"/>
    </div>
    
    {{-- Invocamos el componente para gestionar las TARIFAS de este proveedor --}}
    <div class="mt-8">
        <livewire:calculation.management.provider.components.rate-manager-component
        :provider="$provider"/>
    </div>
</div>