<?php

namespace App\Livewire\Menu\Provider\Components;

use Flux\Flux;
use Livewire\Component;
use Nnjeim\World\World;
use Livewire\Attributes\On;
use App\Models\ProviderZone;
use App\Models\RateProvider;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Validate;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Validator;
use App\Livewire\Traits\GetFlagEmojiTrait;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;

#[Isolate]
class ZoneManagerComponent extends Component
{
    use AdapterValidateLivewireInputTrait,
        GetFlagEmojiTrait,
        WithPagination,
        WithoutUrlPagination;
    
    public RateProvider $provider;
    public $allCountries = [];


    #[Validate('required', message: 'VALIDACIÓN: Debe seleccionar un país')]
    // #[Validate('unique:provider_zones,country_code', message: 'VALIDACIÓN: Esta zona ya existe')]
    public $newCountryCode = '';
    #[Validate('required', message: 'VALIDACIÓN: Debe seleccionar una zona')]
    #[Validate('min:1', message: 'VALIDACIÓN: Debe seleccionar una zona válida (mínimo 1)')]
    #[Validate('integer', message: 'VALIDACIÓN: Debe ingresar un valor entero')]
    public $newZone;

    public $search_country_name;

    public function mount(RateProvider $provider)
    {
        $this->provider = $provider;
        $action = World::setLocale('es')->countries(['fields' => 'iso2,name']);
        if ($action->success) {
            $this->allCountries = collect($action->data)->sortBy('name')->values()->all();
        }
    }

    #[On('editZone')]
    public function editZone($dict)
    {
        $data = json_decode($dict, true);
        $zoneId = $data['zone_id'] ?? null;
        $zoneNumber = $data['zone_number'] ?? null;

        if (!$zoneId || !$zoneNumber) {
            Flux::modal('dichotomic-modal')->close();
            $this->dispatch('escape-enabled');
            $this->dispatch('confirm-validation-modal', 'No se encontró la zona especificada.');
            return;
        }

        $zone = ProviderZone::find($zoneId);
        if ($zone) {
            $zone->update([
                'zone' => $zoneNumber,
            ]);

            $this->dispatch('$refresh');
            $this->reset(['search_country_name']);
            Flux::toast('Zona actualizada correctamente.', 'Éxito');
        }
        Flux::modal('dichotomic-modal')->close();
    }
    
    #[On('editCountry')]
    public function editCountry($dict)
    {
        $data = json_decode($dict, true);
        $zoneId = $data['zone_id'] ?? null;
        $countryName = $data['country_name'] ?? null;
        $countryIso2 = $data['country_iso2'] ?? null;

        if (!$zoneId || !$countryName || !$countryIso2) {
            Flux::modal('dichotomic-modal')->close();
            $this->dispatch('escape-enabled');
            $this->dispatch('confirm-validation-modal', 'No se encontró la zona o el país especificado.');
            return;
        }

        try {
            // 1. Prepara y ejecuta la validación
            Validator::make(
                ['country_name' => $countryName],
                [
                    'country_name' => Rule::unique('provider_zones', 'country_name')
                        ->ignore($zoneId) 
                        ->where('rate_provider_id', $this->provider->id),
                ],
                [
                    'country_name.unique' => 'VALIDACIÓN: Este país ya existe para este proveedor.',
                ]
            )->validate();

        } catch (\Exception $e) {
            Flux::modal('dichotomic-modal')->close();
            $this->dispatch('escape-enabled');
            $this->dispatch('confirm-validation-modal', $e->getMessage());
            return;
        }

        $zone = ProviderZone::find($zoneId);
        if ($zone) {
            $zone->update([
                'country_name' => $countryName,
                'country_code' => $countryIso2,
            ]);

            $this->dispatch('$refresh');
            $this->reset(['search_country_name']);
            Flux::toast('País actualizado correctamente.', 'Éxito');
        }
        Flux::modal('dichotomic-modal')->close();
    }


    public function addZone()
    {

        $variables_to_validate = [
            'newZone',
            'newCountryCode',
        ];

        try {
            $this->validateLivewireInput($variables_to_validate);
        } catch (\Exception $e) {
            $this->dispatch('confirm-validation-modal', $e->getMessage());

            $this->validateLivewireInput($variables_to_validate);
            return;
        }

        try {
            $dataToValidate = [
                'country_code' => $this->newCountryCode,
                'zone' => $this->newZone,
            ];

            $rules = [
                'country_code' => [
                    Rule::unique('provider_zones', 'country_code')
                        ->where('rate_provider_id', $this->provider->id),
                ],
            ];

            // 4. Define el mensaje de error personalizado.
            $messages = [
                'country_code.unique' => 'VALIDACIÓN: Este país ya existe para este proveedor.',
            ];

            // 5. Crea y ejecuta el validador.
            Validator::make($dataToValidate, $rules, $messages)->validate();

        } catch (\Exception $e) {
            $this->dispatch('escape-enabled');
            $this->dispatch('confirm-validation-modal', $e->getMessage());
            $this->addError('newCountryCode', $e->getMessage());
            return;
        }

        $countryName = collect($this->allCountries)->firstWhere('iso2', $this->newCountryCode)['name'] ?? $this->newCountryCode;
        ProviderZone::create(['rate_provider_id' => $this->provider->id, 'country_code' => $this->newCountryCode, 'country_name' => $countryName, 'zone' => $this->newZone]);
        $this->reset(['newCountryCode', 'newZone']);

        Flux::toast('Zona añadida correctamente.', 'Éxito');
    }

    #[On('deleteZone')]
    public function deleteZone($zoneId)
    {
        ProviderZone::find($zoneId)->delete();
        Flux::toast('Zona eliminada correctamente.', 'Éxito');
        Flux::modal('dichotomic-modal')->close();
    }

    public function render()
    {
        // 1. Inicia la construcción de la consulta
        $query = $this->provider->zones()->orderBy('country_name', 'asc');

        // 2. Aplica el filtro de búsqueda SI existe un término de búsqueda
        if ($this->search_country_name) {
            $searchTerm = strtolower($this->search_country_name);
            // Asumo que la columna a buscar es 'country_name', no 'name'
            $query->whereRaw('LOWER(country_name) LIKE ?', ['%' . $searchTerm . '%']);
        }

        // 3. Pagina el resultado final de la consulta construida
        return view('livewire.menu.provider.components.zone-manager-component', [
            'configuredZones' => $query->paginate(5)
        ]);
    }
}
