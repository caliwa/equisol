<div>
    <form wire:submit.prevent="calculate" class="p-4 space-y-4 border rounded-lg bg-white shadow">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="provider" class="block text-sm font-medium text-gray-700">Proveedor</label>
                <select id="provider" wire:model.live="selectedProvider" class="w-full mt-1 form-select">
                    <option value="">Seleccione...</option>
                    @foreach($providers as $provider)
                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                    @endforeach
                </select>
                @error('selectedProvider') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="country" class="block text-sm font-medium text-gray-700">País de Destino</label>
                <select id="country" wire:model="selectedCountry" class="w-full mt-1 form-select" @if(!$countries) disabled @endif>
                    <option value="">Seleccione un proveedor primero...</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->country_code }}">{{ $country->country_name }}</option>
                    @endforeach
                </select>
                @error('selectedCountry') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label for="weight" class="block text-sm font-medium text-gray-700">Peso (KG)</label>
                <input type="number" step="0.1" id="weight" wire:model="weight" class="w-full mt-1 form-input">
                @error('weight') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <button type="submit" class="px-4 py-2 font-bold text-white bg-blue-600 rounded hover:bg-blue-700">
            Calcular Tarifa
        </button>
    </form>

    <div wire:loading wire:target="calculate" class="mt-4 p-4 text-gray-600">
        Calculando...
    </div>
    
    @if($result)
    <div class="p-4 mt-4 border-t" wire:loading.remove>
        <h3 class="text-lg font-bold text-gray-800">Resultado del Cálculo:</h3>
        @if(isset($result['error']))
            <div class="p-4 mt-2 text-red-700 bg-red-100 rounded-md">{{ $result['error'] }}</div>
        @else
            <p class="text-4xl font-bold text-gray-900">{{ number_format($result['price'], 2) }} <span class="text-lg font-normal text-gray-500">USD</span></p>
            <p class="text-sm text-gray-600">
                Tarifa aplicada para el rango de **{{ $result['weight_tier'] }} KG** en la **Zona {{ $result['zone'] }}**.
            </p>
        @endif
    </div>
    @endif
</div>