<div class="p-6">
    <a href="{{ route('providers.index') }}" class="text-blue-600 hover:underline">&larr; Volver a la lista de proveedores</a>

    <h1 class="text-3xl font-bold">Gestionando: <span class="text-indigo-600">{{ $provider->name }}</span></h1>
    
    {{-- Invocamos el componente para gestionar las ZONAS de este proveedor --}}
    <div class="mt-8">
        <livewire:menu.provider.components.zone-manager-component
        :provider="$provider"/>
    </div>
    
    {{-- Invocamos el componente para gestionar las TARIFAS de este proveedor --}}
    <div class="mt-8">
        <livewire:menu.provider.components.rate-manager-component
        :provider="$provider"/>
    </div>
</div>