@assets
<style>
    .btn-builder { @apply p-4 rounded-md font-bold text-lg border border-gray-300 hover:bg-gray-200 transition-all; }
    .form-input { @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50; }
    .form-select { @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50; }
</style>
@endassets
<div x-data="{
    isVisibleCalculationStrategyComponent: $wire.entangle('isVisibleCalculationStrategyComponent').live,
}"
@if(config('modalescapeeventlistener.is_active')) @keydown.escape.window.prevent="closeTopModal()" @endif
>
{{-- MARK: Modal --}}
@if($isVisibleCalculationStrategyComponent)
    <div x-show="isVisibleCalculationStrategyComponent"
        x-effect="
            if (isVisibleCalculationStrategyComponent && !modalStack.includes('isVisibleCalculationStrategyComponent')) {
                modalStack.push('isVisibleCalculationStrategyComponent');
                escapeEnabled = true; removeTabTrapListener();
            } else if (!isVisibleCalculationStrategyComponent) {
                modalStack = modalStack.filter(id => id !== 'isVisibleCalculationStrategyComponent');
                const element = document.getElementById('isVisibleCalculationStrategyComponent');
                if(element){
                    element.classList.add('fade-out-scale');
                }
            }
            focusModal(modalStack[modalStack.length - 1]);
        "
        >
        <div class="fixed top-0 left-0 w-screen h-screen bg-gray-900/50 backdrop-blur-lg"
        style="z-index: {{$zIndexModal + 99}};"></div>
    </div>
    <div x-show="isVisibleCalculationStrategyComponent"
    x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90" id="isVisibleCalculationStrategyComponent"
        class="fixed inset-0 items-center justify-center overflow-x-hidden overflow-y-auto transform-gpu top-4 md:inset-0 h-modal sm:h-full fade-in-scale"
        style="z-index: {{$zIndexModal + 99 + 1}};">
        <div class="relative w-full h-full">
            <div class="absolute inset-0 flex items-start justify-center mt-24 pointer-events-none">
                <button class="inline-flex items-center justify-center p-3 text-sm font-medium text-center text-white bg-gray-300 border rounded cursor-none border-amber-800 hover:bg-blue-800 focus:ring-4 focus:ring-primary-300 sm:w-auto">
                    <div class="flex items-center justify-center">
                        <i class="text-4xl font-bold text-black fa-solid fa-user-tie"></i>
                    </div>
                </button>
            </div>
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                        <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                                    <h3 class="flex justify-between items-center text-lg font-medium leading-6 text-gray-900 uppercase">
                                        <span>● Formulación</span>
                                        <flux:button icon="x-mark" variant="subtle" 
                                            wire:click="CloseModalClick('isVisibleCalculationStrategyComponent')"
                                            x-on:click="isVisibleCalculationStrategyComponent = false" 
                                        />
                                    </h3>
                                    <div class="space-y-4 p-4 bg-white rounded-lg shadow">
                                        <div class="border-b border-gray-200">
                                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                                <button wire:click="setMode('formula')" class="{{ $mode === 'formula' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                                    Modo Fórmula
                                                </button>
                                                <button wire:click="setMode('rules')" class="{{ $mode === 'rules' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                                    Modo Reglas por Pasos
                                                </button>
                                            </nav>
                                        </div>

                                        @if ($mode === 'formula')
                                            <div class="space-y-4">
                                                <div class="p-3 bg-white border border-gray-300 rounded-md text-xl font-mono text-right min-h-[50px]">
                                                    {{ $expression ?: '...' }}
                                                </div>
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    <div class="space-y-2">
                                                        <h4 class="font-bold">Variables</h4>
                                                        <div class="flex items-center gap-2 mb-2 p-2 border rounded-md bg-gray-50">
                                                            <input type="text" 
                                                                wire:model="newVariableName" 
                                                                wire:keydown.enter="addVariable"
                                                                class="form-input w-full text-sm" 
                                                                placeholder="Nombre de variable...">
                                                            <button wire:click="addVariable" class="btn-builder bg-gray-200 text-sm !p-2">
                                                                + Añadir
                                                            </button>
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            @foreach ($availableVariables as $var)
                                                                <button wire:click="addToken('{{ $var }}')" class="btn-builder bg-green-100 text-green-800">{{ $var }}</button>
                                                            @endforeach
                                                        </div>
                                                        <h4 class="font-bold mt-2">Funciones</h4>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <button wire:click="addToken('max(')" class="btn-builder bg-purple-100 text-purple-800">max(</button>
                                                            <button wire:click="addToken('min(')" class="btn-builder bg-purple-100 text-purple-800">min(</button>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-3 gap-2">
                                                        @foreach ([7, 8, 9, 4, 5, 6, 1, 2, 3] as $num)
                                                            <button wire:click="addToken('{{ $num }}')" class="btn-builder bg-gray-100">{{ $num }}</button>
                                                        @endforeach
                                                        <button wire:click="addToken('0')" class="btn-builder bg-gray-100 col-span-2">0</button>
                                                        <button wire:click="addToken('.')" class="btn-builder bg-gray-100">.</button>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <button wire:click="backspace()" class="btn-builder bg-red-100 text-red-700">⌫</button>
                                                        <button wire:click="clearExpression()" class="btn-builder bg-red-100 text-red-700">C</button>
                                                        @foreach (['/', '*', '-', '+', '(', ')', ','] as $op)
                                                            <button wire:click="addToken('{{ $op }}')" class="btn-builder bg-blue-100 text-blue-800">{{ $op }}</button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="p-4 bg-gray-100 rounded-md border">
                                                    <h3 class="font-bold mb-2">🧪 Probar Fórmula</h3>
                                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                                        @foreach ($availableVariables as $var)
                                                            <div>
                                                                <label class="text-sm">{{ $var }}</label>
                                                                <input type="number" wire:model.live="testVariables.{{$var}}" class="form-input">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="mt-4">
                                                        <button wire:click="testExpression" class="bg-indigo-600 text-white font-semibold py-2 px-4 rounded-md hover:bg-indigo-700">Probar</button>
                                                        @if(!is_null($testResultFormula))
                                                            <span class="ml-4 font-mono text-lg p-2 bg-green-200 rounded">Resultado: <strong class="font-bold">{{ $testResultFormula }}</strong></span>
                                                        @endif
                                                        @if($testError)
                                                            <span class="ml-4 font-mono text-sm p-2 bg-red-200 text-red-800 rounded">{{ $testError }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                            <div class="space-y-6">
                                                <div class="space-y-4">
                                                    @forelse ($ruleSet['rules'] as $ruleIndex => $rule)
                                                        <div wire:key="rule-{{ $ruleIndex }}" class="p-4 bg-white border border-gray-200 rounded-md shadow-sm">
                                                            <div class="flex items-start gap-4">
                                                                <div class="flex-grow space-y-2">
                                                                    <span class="text-sm font-semibold text-gray-500">SI...</span>
                                                                    @foreach ($rule['conditions'] as $conditionIndex => $condition)
                                                                        <div wire:key="condition-{{ $ruleIndex }}-{{ $conditionIndex }}" class="flex items-center gap-2">
                                                                            @if ($conditionIndex > 0)<span class="text-sm font-bold text-gray-600">Y</span>@endif
                                                                            <select wire:model.live="ruleSet.rules.{{ $ruleIndex }}.conditions.{{ $conditionIndex }}.variable" class="form-select"><option>-- variable --</option>@foreach ($availableVariables as $variable)<option value="{{ $variable }}">{{ $variable }}</option>@endforeach</select>
                                                                            <select wire:model.live="ruleSet.rules.{{ $ruleIndex }}.conditions.{{ $conditionIndex }}.operator" class="form-select"><option>-- operador --</option>@foreach ($availableOperators as $operator)<option value="{{ $operator }}">{{ $operator }}</option>@endforeach</select>
                                                                            <input type="number" wire:model.live="ruleSet.rules.{{ $ruleIndex }}.conditions.{{ $conditionIndex }}.value" class="form-input w-full" placeholder="Valor">
                                                                            <button wire:click="removeCondition({{ $ruleIndex }}, {{ $conditionIndex }})" class="text-red-500 hover:text-red-700">&times;</button>
                                                                        </div>
                                                                    @endforeach
                                                                    <button wire:click="addCondition({{ $ruleIndex }})" class="text-sm text-blue-600 hover:underline">+ Añadir condición 'Y'</button>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <span class="text-sm font-semibold text-gray-500">ENTONCES...</span>
                                                                    <div class="flex items-center gap-2">
                                                                        <span class="text-lg font-bold">VALOR =</span>
                                                                        <input type="number" wire:model.live="ruleSet.rules.{{ $ruleIndex }}.result" class="form-input font-bold" placeholder="Resultado">
                                                                    </div>
                                                                </div>
                                                                @if($ruleIndex !== 0)
                                                                    <button wire:click="removeRule({{ $ruleIndex }})" class="p-2 text-gray-400 hover:text-red-600">&times;</button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <p class="text-center text-gray-500">No hay reglas definidas.</p>
                                                    @endforelse
                                                </div>
                                                <button wire:click="addRule" class="w-full py-2 px-4 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700">+ Añadir Nueva Regla (SI... ENTONCES...)</button>
                                                <div class="p-4 bg-gray-100 rounded-md border">
                                                    <h3 class="font-bold mb-2">🧪 Probar Reglas</h3>
                                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                                        @foreach ($availableVariables as $var)
                                                            <div>
                                                                <label class="text-sm">{{ $var }}</label>
                                                                <input type="number" wire:model.live="testVariables.{{$var}}" class="form-input">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="mt-4">
                                                        <button wire:click="testRules" class="bg-indigo-600 text-white font-semibold py-2 px-4 rounded-md hover:bg-indigo-700">Probar</button>
                                                        @if(!is_null($testResultRules))
                                                            <span class="ml-4 font-mono text-lg p-2 bg-gray-200 rounded">Resultado: <strong class="font-bold">{{ number_format($testResultRules, 2) }}</strong></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                        <div class="flex justify-end pt-4 border-t mt-4">
                                            <button 
                                                wire:click="save"
                                                @click="loadingSpinner($event);"
                                                class="py-2 px-6 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700">
                                                Guardar Configuración
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

</div>
