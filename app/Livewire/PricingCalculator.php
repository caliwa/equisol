<?php
namespace App\Livewire;

use App\Models\ProviderRate;
use App\Models\ProviderZone;
use App\Models\RateProvider;
use Livewire\Component;

class PricingCalculator extends Component
{
    public $providers;
    public $countries = [];

    public $selectedProvider = '';
    public $selectedCountry = '';
    public $weight = 0.5;
    public $result = null;

    public function mount()
    {
        $this->providers = RateProvider::all();
    }

    public function updatedSelectedProvider($providerId)
    {
        if ($providerId) {
            $this->countries = ProviderZone::where('rate_provider_id', $providerId)
                ->orderBy('country_name', 'asc')
                ->get();
        } else {
            $this->countries = [];
        }
        $this->selectedCountry = '';
        $this->result = null;
    }

    public function calculate()
    {
        $this->validate([
            'selectedProvider' => 'required',
            'selectedCountry' => 'required',
            'weight' => 'required|numeric|min:0.1',
        ]);
        
        $provider = RateProvider::find($this->selectedProvider);

        // --- LÓGICA DE CÁLCULO INTEGRADA EN EL COMPONENTE ---
        // 1. Encontrar la zona
        $zoneRecord = ProviderZone::where('rate_provider_id', $provider->id)
            ->where('country_code', strtoupper($this->selectedCountry))
            ->first();

        if (!$zoneRecord) {
            $this->result = ['error' => 'País no encontrado para este proveedor'];
            return;
        }
        $zone = $zoneRecord->zone;

        // 2. Encontrar la tarifa
        $rateRecord = ProviderRate::where('rate_provider_id', $provider->id)
            ->where('zone', $zone)
            ->where('weight_kg', '>=', $this->weight)
            ->orderBy('weight_kg', 'asc')
            ->first();
            
        if (!$rateRecord) {
            $this->result = ['error' => 'El peso excede el límite de la tabla de tarifas'];
            return;
        }
        
        $this->result = [
            'price' => $rateRecord->price,
            'weight_tier' => $rateRecord->weight_kg,
            'zone' => $zone,
        ];
        // --- FIN DE LA LÓGICA INTEGRADA ---
    }

    public function render()
    {
        return view('livewire.pricing-calculator');
    }
}