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

    public function mount(RateProvider $provider)
    {
        $this->provider = $provider;
        $this->loadRates();
    }

    public function loadRates()
    {
        $this->ratesByWeight = $this->provider->rates()->orderBy('weight_kg', 'asc')->get()->groupBy('weight_kg')->map(fn ($rates) => $rates->keyBy('zone'));
        $this->zones = $this->provider->rates()->distinct()->orderBy('zone', 'asc')->pluck('zone');
        foreach ($this->zones as $zone) {
            $this->newPrices[$zone] = 0;
        }
    }

    public function updateRate($rateId, $newPrice)
    {
        $rate = ProviderRate::find($rateId);
        if ($rate && is_numeric($newPrice)) {
            $rate->price = $newPrice;
            $rate->save();
        }
        // No es necesario recargar toda la tabla para un solo cambio
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
