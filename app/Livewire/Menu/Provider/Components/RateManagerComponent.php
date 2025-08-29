<?php

namespace App\Livewire\Menu\Provider\Components;

use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\ProviderRate;
use App\Models\RateProvider;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Validator;
use App\Livewire\Traits\ResetValidationWrapperTrait;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;

#[Isolate]
class RateManagerComponent extends Component
{
    use AdapterValidateLivewireInputTrait,
        ResetValidationWrapperTrait;
    
    public RateProvider $provider;
    public $ratesByWeight = [];
    public $zones = [];
    #[Validate('required', message: 'VALIDACIÓN: Debe ingresar un peso')]
    #[Validate('numeric', message: 'VALIDACIÓN: El precio debe ser un peso válido')]
    #[Validate('gt:0', message: 'VALIDACIÓN: El precio debe ser un peso mayor que 0')]
    public $newWeight = '';
    public $newPrices = [];

    #[Validate('required', message: 'VALIDACIÓN: Debe ingresar un valor numérico.')]
    #[Validate('numeric', message: 'VALIDACIÓN: El valor debe ser un número.')]
    #[Validate('min:0.00', message: 'VALIDACIÓN: El valor debe ser mayor o igual a 0.')]
    #[Validate('max:999999.99', message: 'VALIDACIÓN: El valor no puede ser mayor a 999999.99.')]
    public float $numericPriceRate;

    public $ZoneIdRate;

    public $weightKeys = [];
    public $originalWeight;
    public $currentWeight;
    public $previousWeight;
    public $nextWeight;

    public function mount(RateProvider $provider)
    {
        $this->provider = $provider;
        $this->loadRates();
    }

    public function loadRates()
    {
        $allRates = $this->provider->rates()->orderBy('weight_kg', 'asc')->get();

        $this->ratesByWeight = $allRates->groupBy('weight_kg')
            ->map(function ($rates) {
                return $rates->keyBy('zone');
            });

        $this->zones = $allRates->pluck('zone')->unique()->sort();

        // --- LÍNEA AÑADIDA ---
        // Obtenemos una lista simple y ordenada de los pesos para usarla en el frontend.
        $this->weightKeys = $this->ratesByWeight->keys()->toArray();

        // Inicializa los precios para la nueva fila
        foreach ($this->zones as $zone) {
            $this->newPrices[$zone] = 0;
        }
    }

    public function updateWeight()
    {
        $weightIndex = array_search($this->originalWeight, $this->weightKeys);
        $previousWeight = $this->weightKeys[$weightIndex - 1] ?? 0;
        $nextWeight = $this->weightKeys[$weightIndex + 1] ?? null;


        $rules = [
            'newWeight' => [
                'required',
                'numeric',
                "gt:{$previousWeight}",
            ]
        ];

        if ($nextWeight !== null) {
            $rules['newWeight'][] = "lt:{$nextWeight}";
        }

        $rules['newWeight'][] = Rule::unique('provider_rates', 'weight_kg')
            ->where('rate_provider_id', $this->provider->id)
            ->ignore($this->originalWeight, 'weight_kg');

        $messages = [
            'newWeight.required' => 'El campo de nuevo peso es obligatorio.',
            'newWeight.numeric' => 'El valor debe ser numérico.',
            'newWeight.gt' => 'El peso debe ser mayor que :value kg.',
            'newWeight.lt' => 'El peso debe ser menor que :value kg.',
            'newWeight.unique' => 'El valor de este peso ya está en uso.',
        ];

        try {
                $validatedData = $this->validate($rules, $messages);
            } catch (\Exception $e) {
                $this->dispatch('x-unblock-weight-flyout-modal');
                $this->dispatch('escape-enabled');
                $this->addError('newWeight', $e->getMessage());
                $validatedData = $this->validate($rules, $messages);
                return;
            }

        // 5. Si la validación pasa, actualizar el registro
        ProviderRate::where('rate_provider_id', $this->provider->id)
            ->where('weight_kg', $this->originalWeight)
            ->update(['weight_kg' => $validatedData['newWeight']]);

        $this->loadRates();

        Flux::toast('Peso actualizado exitosamente.');
        Flux::modal('edit-weight-modal')->close();
        $this->dispatch('escape-enabled');
    }

    public function updateRate()
    {
        $variables_to_validate = [
            'numericPriceRate',
        ];

        try {
            $this->validateLivewireInput($variables_to_validate);
        } catch (\Exception $e) {
            $this->dispatch('x-unblock-loading-tariff-modal');
            $this->dispatch('escape-enabled');
            $this->validateLivewireInput($variables_to_validate);
        }

        
        $rate = ProviderRate::find($this->ZoneIdRate);
        if ($rate && is_numeric($this->numericPriceRate)) {
            $rate->price = $this->numericPriceRate;
            $rate->save();
            Flux::modal('tariff-modal')->close();
            Flux::toast('Tarifa modificada correctamente.', 'Éxito');
            return;
        }
    }
    
    public function addRateRow()
    {
        $variables_to_validate = [
            'newWeight',
        ];

        try {
            $this->validateLivewireInput($variables_to_validate);
        } catch (\Exception $e) {
            $this->dispatch('confirm-validation-modal', $e->getMessage());

            $this->validateLivewireInput($variables_to_validate);
            return;
        }

        try {
            Validator::make(
                ['weight_kg' => $this->newWeight],
                [
                    'weight_kg' => Rule::unique('provider_rates', 'weight_kg')
                        ->where('rate_provider_id', $this->provider->id),
                ],
                [
                    'weight_kg.unique' => 'VALIDACIÓN: Este peso ya ha sido agregado para este proveedor.',
                ]
            )->validate();

        } catch (\Exception $e) {
            $this->dispatch('escape-enabled');
            $this->dispatch('confirm-validation-modal', $e->getMessage());
            $this->addError('newWeight', $e->getMessage());
            return;
        }

        for ($zone = 1; $zone <= 7; $zone++) {
            ProviderRate::updateOrCreate(
                [
                    'rate_provider_id' => $this->provider->id, 
                    'weight_kg' => $this->newWeight, 
                    'zone' => $zone
                ],
                ['price' => 0]
            );
        }

        $this->reset(['newWeight', 'newPrices']);
        $this->loadRates();
        Flux::toast('Peso agregado exitosamente.', 'Éxito');
    }

    #[On('deleteRateRow')]
    public function deleteRateRow($weight)
    {
        ProviderRate::where('rate_provider_id', $this->provider->id)->where('weight_kg', $weight)->delete();
        $this->loadRates();
        Flux::toast('Peso eliminado correctamente.', 'Éxito');
        Flux::modal('dichotomic-modal')->close();
    }


    public function render()
    {
        return view('livewire.menu.provider.components.rate-manager-component');
    }
}
