<div class="p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
    <flux:heading size="md">Gestión de Zonas</flux:heading>

    <form wire:submit.prevent="addZone" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end p-4 bg-gray-50 dark:bg-gray-800/50 border dark:border-gray-700 rounded-md">
        <flux:field>
            <flux:label>País</flux:label>
            <flux:select variant="listbox" searchable wire:model="newCountryCode" placeholder="-- Seleccione un País --">
                @foreach($allCountries as $country)
                    <flux:select.option value="{{ $country['iso2'] }}">
                        <span class="flex items-center justify-start">
                            <span class="mr-2">{!! $this->getFlagEmoji($country['iso2']) !!}</span>
                            <span>{{ $country['name'] }}</span>
                        </span>
                    </flux:select.option>
                @endforeach
            </flux:select>
        </flux:field>
        
        <flux:field>
            <flux:label>Nº de Zona</flux:label>
            <flux:input type="number" wire:model="newZone" placeholder="Ej: 1" />
        </flux:field>
        
        <flux:button type="submit" variant="primary" icon="plus">Añadir Zona</flux:button>
    </form>

    <flux:label>Buscar País</flux:label>
    <flux:input type="text" wire:model.live.debounce.500ms="search_country_name" placeholder="Escriba el nombre del país a buscar..." />

    <flux:table>
        <flux:table.columns>
            <flux:table.column>País</flux:table.column>
            <flux:table.column align="center">Código</flux:table.column>
            <flux:table.column>Zona</flux:table.column>
            <flux:table.column align="center">
                <flux:badge icon="user-circle" color="rose">Acciones</flux:badge>
            </flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse($configuredZones as $idx => $zone)
                <flux:table.row wire:key="zone-{{ $zone->id }}">
                    <flux:table.cell class="font-medium">
                        <flux:select 
                            variant="listbox" searchable wire:model.change="configuredZones.{{ $idx }}.country_name" placeholder="Elige un país">
                            @foreach($allCountries as $country)
                                @if($country['name'] == $zone->country_name)
                             
                                    <flux:select.option 
                                        wire:key="country-{{ $country['id'] }}"
                                        selected
                                        value="{{ $country['iso2'] }}"
                                        >
                                        <span class="flex items-center justify-start">
                                            <span class="mr-2">{!! $this->getFlagEmoji($country['iso2']) !!}</span>
                                            <span>{{ $country['name'] }}</span>
                                        </span>
                                    </flux:select.option>
                                @else
                                    <div>
                                        <flux:select.option
                                            wire:key="country-{{ $country['id'] }}"
                                            x-on:click.prevent="prepareDichotomic({
                                                method: 'editCountry',
                                                param: '{{ json_encode([
                                                            'country_name' => $country['name'],
                                                            'zone_id' => $zone->id,
                                                            'country_iso2' => $country['iso2'],
                                                        ]) }}',
                                                heading: 'Cambiar País',
                                                message: `¿Estás seguro de que quieres cambiar al país {!! $this->getFlagEmoji($country['iso2']) . ' ' . $country['name'] !!}?`,
                                                modalDichotomicBtnText: 'Cambiar'
                                            });"
                                            value="{{ $country['iso2'] }}"
                                        >
                                            <span class="flex items-center justify-start">
                                                <span class="mr-2">{!! $this->getFlagEmoji($country['iso2']) !!}</span>
                                                <span>{{ $country['name'] }}</span>
                                            </span>
                                        </flux:select.option>
                                    </div>
                                @endif
                            @endforeach
                        </flux:select>

                        </span>
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <flux:badge color="secondary">{{ $zone->country_code }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:select variant="listbox" size="sm" placeholder="Seleccione una zona...">
                            @foreach(range(1, 7) as $idx => $zoneNumber)
                                @if($zone->zone == $zoneNumber)
                                    <flux:select.option
                                        wire:key="zoneNumber-{{ $idx }}"
                                        value="{{ $zoneNumber }}"
                                        selected
                                    >
                                        Nro. {{ $zoneNumber }}
                                    </flux:select.option>
                                @else
                                    <div>
                                        <flux:select.option
                                            wire:key="zoneNumber-{{ $idx }}"
                                            value="{{ $zoneNumber }}"
                                            x-on:click.prevent="prepareDichotomic({
                                                method: 'editZone',
                                                param: '{{ json_encode([
                                                    'zone_id' => $zone->id,
                                                    'zone_number' => $zoneNumber
                                                ]) }}',
                                                heading: 'Cambiar Zona',
                                                message: `¿Estás seguro de que quieres cambiar a la zona Nro. '{{ $zoneNumber }}'?`,
                                                modalDichotomicBtnText: 'Cambiar'
                                            });"
                                        >
                                            Nro. {{ $zoneNumber }}
                                        </flux:select.option>
                                    </div>
                                @endif
                            @endforeach
                        </flux:select>
                    </flux:table.cell>
                    <flux:table.cell align="center">
                        <flux:button
                            variant="primary"
                            color="red"
                            size="sm"
                            @click="prepareDichotomic({
                                method: 'deleteZone',
                                param: {{ $zone->id }},
                                heading: 'Eliminar Zona',
                                message: `¿Estás seguro de eliminar la zona para '{{ $zone->country_name }}'?`,
                                modalDichotomicBtnText: 'Borrar'
                            })"
                        >
                            Eliminar
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="4">
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">No hay zonas configuradas.</p>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
    <flux:pagination :paginator="$configuredZones" />

</div>