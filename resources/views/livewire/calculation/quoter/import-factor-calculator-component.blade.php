<div class="mt-2 bg-slate-50 dark:bg-zinc-900 p-6 rounded-xl shadow-lg border border-slate-200 dark:border-zinc-700 max-w-7xl mx-auto">

    <flux:breadcrumbs>
        <flux:breadcrumbs.item href="#" icon="globe-alt" />
        <flux:breadcrumbs.item href="#">Cotizador F.I</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading class="mt-2" size="xl">Gestión Base de datos - Maestro Gastos</flux:heading>
    <div wire:dirty>Esperando sincronización...</div> 
    <div wire:dirty.remove>Los cambios están sincronizados.</div>

    <div class="grid grid-cols-1 min-[1374px]:grid-cols-3 gap-8">

        <div class="lg:col-span-1 space-y-8">

            <div class="flex flex-col items-center">
                <div class="flex h-20 w-32 items-center justify-center rounded-md border border-dashed my-4">
                    <img src="{{ asset('img/equisol-logo-1.png') }}" 
                    alt="Logo Empresa" 
                    class="max-h-full max-w-full object-contain mx-auto my-auto" />
                </div>

                <flux:fieldset class="w-full">
                    <flux:legend class="text-base font-semibold text-slate-700 dark:text-slate-300 mb-2">Cambio</flux:legend>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:label>TRM</flux:label>
                            <flux:input size="sm" type="text" id="trm" wire:model.fill="trm" class="mt-1" />
                        </div>
                        <div>
                            <flux:label>$/€</flux:label>
                            <flux:input size="sm" type="text" id="exchange_eur" wire:model.fill="exchange_eur" class="mt-1" />
                        </div>
                    </div>
                </flux:fieldset>
            </div>

            <flux:fieldset>
                <flux:legend class="text-base font-semibold text-slate-700 dark:text-slate-300">Transporte</flux:legend>
                <flux:radio.group wire:model="role" variant="segmented">
                    <flux:radio wire:click="setTransportMode('maritime')" label="Marítimo" icon="pencil-square" />
                    <flux:radio wire:click="setTransportMode('aerial')" label="Aéreo" icon="paper-airplane" />
                    <flux:radio wire:click="setTransportMode('courrier')" label="Courrier" icon="truck" />
                </flux:radio.group>
            </flux:fieldset>

            <flux:button wire:click="calculateBtn" class="mt-4 w-full max-[1374px]:hidden" color="indigo" icon="calculator">
                    Calcular
            </flux:button>

            {{-- <div>
                <label for="transit_days" class="block text-sm font-medium text-slate-600 dark:text-slate-400">Tte & Consolid.</label>
                <flux:input.group>
                    <flux:input size="sm" type="text" id="transit_days" wire:model.fill="transit_days" class="block w-full"/>
                    <flux:input.group.suffix>días</flux:input.group.suffix>
                </flux:input.group>
            </div> --}}
        </div>


        <div class="block">
            <flux:legend class="top-0 text-base font-semibold text-slate-700 dark:text-slate-300">Origen : {{$origin ?? 'N/A'}}</flux:legend>
            <div class="lg:col-span-1 max-h-[500px] overflow-y-auto">
                <flux:fieldset>

                    <flux:radio.group
                        class="flex-col"
                        variant="cards"
                        wire:model.live="origin"
                    >
                        @foreach($origins_countries as $country)
                            <flux:radio
                                wire:key="origin-{{ $country['id'] }}"
                                value="{{ $country['name'] }}"
                                description="País seleccionable para cálculo F.I"
                                label="{!! $this->getFlagEmoji($country['iso2']) !!} {{ $country['name'] }}"
                            />
                        @endforeach
                    </flux:radio.group>

                </flux:fieldset>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-8">
                <flux:fieldset>
                    <div class="flex gap-4 items-center">
                        <flux:button
                            @click="loadingSpinner($event)"
                            wire:click="AddValueInputVariables()"
                            variant="primary"
                            color="emerald"
                            icon="plus"
                            >
                        </flux:button>
                        <flux:description>Oprime para agregar un nuevo pallet</flux:description>
                    </div>

                <flux:legend class="text-base font-semibold text-slate-700 dark:text-slate-300">Equipo</flux:legend>
                <div class="mt-2 space-y-4">
                        <flux:label>Dimensiones (m)</flux:label>
                        <div class="max-h-[75px] overflow-y-auto">
                            @foreach(range(0, count($variables_pallet) - 1) as $idx)
                                <div class="mt-1 grid grid-cols-5 gap-2 items-center">
                                    <flux:badge icon="arrows-pointing-in" class="justify-center" color="lime">Nro.{{$idx + 1}}</flux:badge>
                                    <flux:input 
                                        wire:model.live="variables_pallet.{{$idx}}.width"
                                        wire:keydown="CalculatePallet('{{$idx}}', 'width')"
                                        @input="$wire.ResetShowValues();"
                                        size="sm" type="number" placeholder="A"></flux:input>
                                    <flux:input 
                                        wire:model.live="variables_pallet.{{$idx}}.length"
                                        wire:keydown="CalculatePallet('{{$idx}}', 'length')"
                                        @input="$wire.ResetShowValues();"
                                        size="sm" type="number" placeholder="L"></flux:input>
                                    <flux:input 
                                        wire:model.live="variables_pallet.{{$idx}}.height"
                                        wire:keydown="CalculatePallet('{{$idx}}', 'height')"
                                        @input="$wire.ResetShowValues();"
                                        size="sm" type="number" placeholder="H"></flux:input>
                                    <div class="flex justify-center">
                                        <flux:button icon="x-mark" icon:variant="outline" size="xs" variant="danger"
                                        @click="loadingSpinner($event);"
                                        wire:click="RemoveInputVariables({{ $idx }})"></flux:button>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    <div class="flex gap-2 items-center">
                        <flux:label>Peso (kg)</flux:label>
                        <flux:input
                            size="sm"
                            mask:dynamic="$money($input)" 
                            @input="$wire.ResetShowValues();"
                            wire:model.fill="weight"
                            type="text"
                            id="weight"
                            ></flux:input>
                        <flux:label>Total Vol.</flux:label>
                        <flux:input size="sm" variant="filled" 
                        readonly wire:model.fill="total_volume" type="text" id="total_volume"></flux:input>
                    </div>
                    <flux:description>Solo ingrese números enteros para evitar errores.</flux:description>
                </div>
            </flux:fieldset>

            <div class="space-y-6">
                <div>
                    <flux:label>Arancel</flux:label>
                    <flux:input.group>
                        <flux:input 
                        size="sm" 
                        type="number"
                        id="tariff"
                        @input="$wire.ResetShowValues();"
                        wire:model.fill="tariff" class="block w-full flex-1 rounded-none rounded-l-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-zinc-800 dark:border-zinc-600 dark:text-slate-200"></flux:input>
                        <flux:input.group.suffix>%</flux:input.group.suffix>
                    </flux:input.group>
                <div>
                    <flux:label>Costo</flux:label>
                    <div class="mt-1 flex items-center space-x-2">
                        <flux:input size="sm" 
                        {{-- mask:dynamic="$money($input)" --}}
                        type="number"
                        id="cost"
                        @input="$wire.ResetShowValues();"
                        wire:model.fill="cost"
                        class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-zinc-800 dark:border-zinc-600 dark:text-slate-200"></flux:input>
                        <div class="isolate inline-flex rounded-md shadow-sm">
                            <button type="button" class="relative inline-flex items-center rounded-l-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white ring-1 ring-inset ring-indigo-600 focus:z-10">US$</button>
                            <button type="button" class="relative -ml-px inline-flex items-center rounded-r-md bg-white px-3 py-2 text-sm font-semibold text-slate-900 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:z-10 dark:bg-zinc-700 dark:text-slate-200 dark:ring-zinc-600 dark:hover:bg-zinc-600">€</button>
                        </div>
                    </div>
                </div>

                {{-- <div>
                    <flux:label>Multas y empaque</flux:label>
                    <flux:input.group>
                        <flux:input size="sm" type="text" id="fines_packaging" class="block w-full flex-1 rounded-none rounded-l-md border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-zinc-800 dark:border-zinc-600 dark:text-slate-200"></flux:input>
                        <flux:input.group.suffix>%</flux:input.group.suffix>
                    </flux:input.group>
                </div> --}}
                <flux:button wire:click="calculateBtn" class="mt-4 w-full min-[1374px]:hidden" color="indigo" icon="calculator">
                    Calcular
                </flux:button>
            </div>
        </div>
      </div>
    </div>

    <div class="mt-8 pt-6 border-t border-slate-200 dark:border-zinc-700">
        <div class="bg-white dark:bg-zinc-800/50 p-6 rounded-lg grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-x-8 gap-y-6">
            
            <div class="space-y-2 lg:col-span-2">
                <div class="flex justify-between items-baseline border-b border-dashed pb-1">
                    <span class="text-sm text-slate-500 dark:text-slate-400">EXW (US$)</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-200">{{ number_format($cost_show ?? 0, 2, '.', ',') }}</span>
                </div>
                <div class="flex justify-between items-baseline border-b border-dashed pb-1">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Costos de Origen (US$)</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-200">{{ number_format($origin_cost_show ?? 0, 2, '.', ',') }}</span>
                </div>
                <div class="flex justify-between items-baseline border-b border-dashed pb-1">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Flete (US$)</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-200">{{ number_format($freight_show ?? 0, 2, '.', ',') }}</span>
                </div>
                <div class="flex justify-between items-baseline">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Seguro (US$)</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-200">{{ number_format($insurance_show ?? 0, 2, '.', ',') }}</span>
                </div>
            </div>

            <div class="space-y-2 lg:col-span-1">
                <div class="flex justify-between items-baseline border-b border-dashed pb-1">
                    <span class="text-sm text-slate-500 dark:text-slate-400">CIF (US$)</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-200">{{ number_format($cif_show ?? 0, 2, '.', ',') }}</span>
                </div>
                <div class="flex justify-between items-baseline border-b border-dashed pb-1">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Arancel (US$)</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-200">{{ number_format($tariff_show ?? 0, 2, '.', ',') }}</span>
                </div>
                <div class="flex justify-between items-baseline">
                    <span class="text-sm text-slate-500 dark:text-slate-400">Costos en Destino (US$)</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-200">{{ number_format($destination_costs_show ?? 0, 2, '.', ',') }}</span>
                </div>
            </div>
            
            <div class="md:col-span-3 lg:col-span-2 lg:pl-6 lg:border-l lg:border-slate-200 dark:lg:border-zinc-700 flex flex-col justify-center space-y-3">
                <div class="flex justify-between items-baseline">
                    <span class="font-medium text-slate-600 dark:text-slate-300 text-lg">Costo DDP (US$)</span>
                    <span class="font-bold text-indigo-600 dark:text-indigo-400 text-xl">{{ number_format($ddp_cost ?? 0, 2, '.', ',') }}</span>
                </div>
                <div class="flex justify-between items-baseline bg-emerald-50 dark:bg-emerald-900/50 p-3 rounded-lg">
                    <span class="font-bold text-emerald-800 dark:text-emerald-300 text-lg">Factor de Importación</span>
                    <span class="font-extrabold text-emerald-600 dark:text-emerald-400 text-2xl">{{ number_format($import_factor ?? 0, 1, ',', '.') }} %</span>
                </div>
            </div>
            
        </div>
    </div>
</div>