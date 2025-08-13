<?php

namespace App\Livewire\Menu;

use Flux\Flux;
use App\Models\Rate;
use App\Models\Origin;
use App\Models\Service;
use Livewire\Component;
use Nnjeim\World\World;
use App\Models\Currency;
use App\Models\WeightTier;
use App\Models\ServiceType;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;

class IndexMenuComponent extends Component
{
    use AdapterValidateLivewireInputTrait;

    public array $table_columns = [];
    public array $rows_data = [];
    public array $service_currencies = [];
    
    public string $type_service = 'pu_aereo';
    
    public string $serviceTypeName = 'Pick Up Aéreo';

    #[Validate('required', message: 'VALIDACIÓN: Debe seleccionar un operador.')]
    public string $selectedOperator = '';
    #[Validate('required', message: 'VALIDACIÓN: Debe ingresar un valor numérico.')]
    #[Validate('numeric', message: 'VALIDACIÓN: El valor debe ser un número.')]
    #[Validate('min:0.01', message: 'VALIDACIÓN: El valor debe ser mayor a 0.')]
    #[Validate('max:999999.99', message: 'VALIDACIÓN: El valor no puede ser mayor a 999999.99.')]
    public float $numericValueTariff;
    public $rowIndexTariff;
    public $columnNameTariff;

    // #[Validate('required', message: 'VALIDACIÓN: Debe ingresar un valor numérico.')]
    // #[Validate('numeric', message: 'VALIDACIÓN: El valor debe ser un número.')]
    // #[Validate('min:0.01', message: 'VALIDACIÓN: El valor debe ser mayor a 0.')]
    // #[Validate('max:999999.99', message: 'VALIDACIÓN: El valor no puede ser mayor a 999999.99.')]
    // public float $numericMinimumTariffValue;

    public bool $enableCurrencyFeature = true;
    public bool $showCurrencyRow = true;
    public Collection $currencies;

    #[Validate('required', message: 'Debe ingresar un nombre para el país.')]
    #[Validate('min:3', message: 'El nombre del país debe tener al menos 3 caracteres.')]
    #[Validate('unique:origins,name', message: 'El país ya existe.')]
    public string $newColumnName = '';

    public $countries = [];

    public function mount()
    {

        $action = World::setLocale('es')->countries(['fields' => 'iso2']);

        if ($action->success) {
            $this->countries = $action->data;
        }

        // Solo cargamos las monedas si la función está activada para esta tabla.
        if ($this->enableCurrencyFeature) {
            $this->currencies = Currency::all();
        }
        $this->loadRateTableData();
    }

    function getFlagEmoji(string $countryCode): string
    {
        if (strlen($countryCode) !== 2) {
            return '';
        }

        $codePoints = array_map(
            fn($char) => 127397 + ord(strtoupper($char)),
            str_split($countryCode)
        );

        return mb_convert_encoding('&#' . implode(';&#', $codePoints) . ';', 'UTF-8', 'HTML-ENTITIES');
    }

    public function toggleCurrencyRow()
    {
        $this->showCurrencyRow = !$this->showCurrencyRow;
    }

    public function loadRateTableData()
    {
        $serviceType = ServiceType::firstOrCreate(['name' => $this->serviceTypeName]);

        $query = Service::where('service_type_id', $serviceType->id)
            ->with(['origin', 'rates.weightTier'])
            ->whereHas('origin')
            ->join('origins', 'services.origin_id', '=', 'origins.id')
            ->orderBy('origins.name')
            ->select('services.*');

        // Si la función de moneda está activada, también cargamos esa relación.
        if ($this->enableCurrencyFeature) {
            $query->with('currency');
        }
        
        $services = $query->get();
        $weightTiers = WeightTier::orderBy('display_order')->get();

        $this->table_columns = [['id' => 'tier_label', 'name' => 'tier_label', 'label' => '']]; //Tarifa
        foreach ($services as $service) {
            $this->table_columns[] = [
                'id' => $service->id,
                'name' => 'service_' . $service->id,
                'label' => $service->origin->name,
                'origin_id' => $service->origin_id
            ];
        }

        if ($this->enableCurrencyFeature) {
            $this->service_currencies = $services->pluck('currency_id', 'id')->map(fn ($id) => $id ?? '')->toArray();
        }

        $this->rows_data = [];
        $minimaRow = ['tier_label' => 'Mínima', 'tier_id' => null];
        foreach ($services as $service) {
            $minimaRow['service_' . $service->id] = number_format($service->minimum_charge, 2, '.', '');
        }
        $this->rows_data[] = $minimaRow;

        foreach ($weightTiers as $tier) {
            $currentRow = ['tier_label' => $tier->label, 'tier_id' => $tier->id];
            foreach ($services as $service) {
                $rate = $service->rates->firstWhere('weight_tier_id', $tier->id);
                $currentRow['service_' . $service->id] = $rate ? number_format($rate->rate_value, 2, '.', '') : '0.00';
            }
            $this->rows_data[] = $currentRow;
        }
        // dd($this->table_columns );
        // dd([$this->rows_data, $this->table_columns]);
    }

    public function save()
    {
        DB::transaction(function () {
            // Guardar nombres de países
            foreach ($this->table_columns as $column) {
                if (isset($column['origin_id'])) {
                    Origin::where('id', $column['origin_id'])->update(['name' => $column['label']]);
                }
            }

            // Guardar monedas (si la función está activada)
            if ($this->enableCurrencyFeature) {
                foreach ($this->service_currencies as $serviceId => $currencyId) {
                    Service::where('id', $serviceId)->update(['currency_id' => $currencyId ?: null]);
                }
            }

            // Guardar tarifas y mínimos
            foreach ($this->rows_data as $row) {
                $tierId = $row['tier_id'];
                $tierLabel = $row['tier_label'];
                if (is_null($tierId) && $tierLabel === 'Mínima') {
                    foreach ($row as $key => $value) {
                        if (str_starts_with($key, 'service_')) {
                            Service::where('id', str_replace('service_', '', $key))->update(['minimum_charge' => $value]);
                        }
                    }
                } elseif (!is_null($tierId)) {
                    WeightTier::where('id', $tierId)->update(['label' => $tierLabel]);
                    foreach ($row as $key => $value) {
                        if (str_starts_with($key, 'service_')) {
                            Rate::updateOrCreate(
                                ['service_id' => str_replace('service_', '', $key), 'weight_tier_id' => $tierId],
                                ['rate_value' => $value]
                            );
                        }
                    }
                }
            }
        });
        Flux::toast('Datos guardados correctamente.');
        $this->loadRateTableData();
        $this->dispatch('EscapeEnabled');
    }

    public function editCountry($dict){
        $dict = (array)json_decode($dict);

        $country_name = $dict['country_name'] ?? null;
        $colIndex = $dict['colIndex'] ?? null;

        $this->table_columns[$colIndex]['label'] = $country_name;

        foreach ($this->table_columns as $column) {
            if (isset($column['origin_id'])) {
                Origin::where('id', $column['origin_id'])->update(['name' => $column['label']]);
            }
        }

        Flux::toast('Datos guardados correctamente.');
        $this->loadRateTableData();
        $this->dispatch('EscapeEnabled');
        Flux::modal('dichotomic-modal')->close();
    }

    public function addRowPercentage(){
        $variables_to_validate = [
            'numericValueTariff',
        ];

        try {
            $this->validateLivewireInput($variables_to_validate);
        } catch (\Exception $e) {
            $this->dispatch('x-unblock-loading-percentage-modal');
            $this->dispatch('EscapeEnabled');
            $this->validateLivewireInput($variables_to_validate);
        }

        $formattedValue = $this->numericValueTariff + 0;

        if(!is_null($this->rowIndexTariff) && !is_null($this->columnNameTariff)) {

            $this->rows_data[$this->rowIndexTariff][$this->columnNameTariff] = $formattedValue;
            $this->save();
            Flux::modal('percentage-modal')->close();
            Flux::toast('Porcentaje modificado correctamente.', 'Éxito');
            return;
        }
    }

    public function addRowTariff(){
        $variables_to_validate = [
            'numericValueTariff',
        ];

        try {
            $this->validateLivewireInput($variables_to_validate);
        } catch (\Exception $e) {
            $this->dispatch('x-unblock-loading-tariff-modal');
            $this->dispatch('EscapeEnabled');
            $this->validateLivewireInput($variables_to_validate);
        }

        $formattedValue = $this->numericValueTariff + 0;

        if(!is_null($this->rowIndexTariff) && !is_null($this->columnNameTariff)) {
            $this->rows_data[$this->rowIndexTariff][$this->columnNameTariff] = $formattedValue;
            $this->save();
            Flux::modal('minimum-tariff-modal')->close();
            Flux::toast('Tarifa agregada correctamente.', 'Éxito');
            return;
        }
    }

    public function addRow()
    {
        $variables_to_validate = [
            'selectedOperator',
            'numericValueTariff',
        ];

        try {
            $this->validateLivewireInput($variables_to_validate);
        } catch (\Exception $e) {
            $this->dispatch('EscapeEnabled');
            $this->validateLivewireInput($variables_to_validate);
        }

        $formattedValue = $this->numericValueTariff + 0;

        $label = $this->selectedOperator . $formattedValue;

        try {
            Validator::make(
                ['label' => $label],
                ['label' => 'unique:weight_tiers,label'], 
                ['label.unique' => 'La tarifa "' . $label . '" ya existe.']
            )->validate();
        } catch (\Exception $e) {
            $this->dispatch('EscapeEnabled');
            $this->addError('numericValueTariff', $e->getMessage());
            return;
        }

        if(!is_null($this->rowIndexTariff) && !is_null($this->columnNameTariff)) {
            $this->rows_data[$this->rowIndexTariff][$this->columnNameTariff] = $label;
            $this->save();
            Flux::modal('operand-modal')->close();
            return;
        }

        $lastTier = WeightTier::orderBy('display_order', 'desc')->first();
        WeightTier::create([
            'label' => $label,
            'min_weight' => ($lastTier->max_weight ?? -1) + 1,
            'max_weight' => ($lastTier->max_weight ?? 0) + 1000,
            'display_order' => ($lastTier->display_order ?? 0) + 1,
        ]);
        Flux::modal('operand-modal')->close();
        Flux::toast('Tarifa agregada correctamente.', 'Éxito');

        $this->loadRateTableData();
    }

    public function addColumn()
    {
        $variables_to_validate = [
            'newColumnName',
        ];

        $this->validateLivewireInput($variables_to_validate);

        DB::transaction(function () {
            $name = $this->newColumnName;
            $serviceType = ServiceType::firstOrCreate(['name' => $this->serviceTypeName]);
            $origin = Origin::create(['name' => $name]);
            Service::create([
                'origin_id' => $origin->id,
                'service_type_id' => $serviceType->id,
                'minimum_charge' => 0,
            ]);
        });
        $this->reset(['newColumnName']);
        Flux::toast('Campo agregado correctamente.', 'Éxito');
        $this->loadRateTableData();
    }

    public function removeColumn($serviceId)
    {
        DB::transaction(function() use ($serviceId) {
            $service = Service::find($serviceId);
            if ($service) {
                $origin = $service->origin;
                $service->delete();
                if ($origin && !$origin->services()->exists()) {
                    $origin->delete();
                }
            }
        });
        Flux::toast('País eliminado correctamente.', 'Éxito');
        Flux::modal('dichotomic-modal')->close();
        $this->loadRateTableData();
    }

    public function removeRow($tierId)
    {
        // sleep(50);
        WeightTier::destroy($tierId);
        Flux::toast('Tarifa eliminada correctamente.', 'Éxito');
        Flux::modal('dichotomic-modal')->close();
        $this->loadRateTableData();
    }

    public function resetValidationWrapper(){
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.menu.index-menu-component');
    }
}
