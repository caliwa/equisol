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
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Collection;
use App\Livewire\Traits\ResetValidationWrapperTrait;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;

#[Isolate]
class IndexMenuComponent extends Component
{
    use AdapterValidateLivewireInputTrait,
        ResetValidationWrapperTrait;

    public array $table_columns = [];
    public array $rows_data = [];
    public array $service_currencies = [];
    public string $type_service = 'pu_aereo';
    public string $serviceTypeName = 'Pick Up Aéreo';

    public int $serviceTypeId;

    #[Validate('required', message: 'VALIDACIÓN: Debe seleccionar un operador.')]
    public string $selectedOperator = '';
    #[Validate('required', message: 'VALIDACIÓN: Debe ingresar un valor numérico.')]
    #[Validate('numeric', message: 'VALIDACIÓN: El valor debe ser un número.')]
    #[Validate('min:0.00', message: 'VALIDACIÓN: El valor debe ser mayor o igual a 0.')]
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

    #[Validate('required', message: 'VALIDACIÓN: Debe ingresar un nombre para el país.')]
    // #[Validate('min:3', message: 'El nombre del país debe tener al menos 3 caracteres.')]
    // #[Validate('unique:origins,name', message: 'El país ya existe.')]
    public string $newColumnName = '';

    public $countries = [];

    // public $showPercentageModal = false;

    // public function updatedShowPercentageModal($value){
    //     if ($value) {
    //         $this->dispatch('x-block-open-percentage-modal');
    //     } else {
    //         $this->dispatch('x-unblock-open-percentage-modal');
    //     }
    // }

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
        $this->SelectMasterTypeService($this->serviceTypeName, true);
        $this->loadRateTableData();

    }

    function SelectMasterTypeService($serviceTypeName, $firstTime = null)
    {
        if($serviceTypeName == 'Flete Courier'){
            $this->redirectRoute('providers.index', navigate:true);
            $this->dispatch('escape-enabled');
            return;
        }
        $this->serviceTypeName = $serviceTypeName;
        $serviceType = ServiceType::firstOrCreate(['name' => $serviceTypeName]);
        $this->serviceTypeId = $serviceType->id;
        $this->loadRateTableData();
        if(is_null($firstTime)){
            Flux::toast('Maestro cambiado éxitosamente.');
        }
        $this->dispatch('escape-enabled');
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
        $this->dispatch('escape-enabled');
    }

    public function loadRateTableData()
    {
        $query = Service::where('service_type_id', $this->serviceTypeId)
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
        $weightTiers = WeightTier::where('service_type_id', $this->serviceTypeId)
                         ->orderBy('display_order')
                         ->get();

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

    public function saveNewCurrencyMaster(){
        DB::transaction(function () {
            if ($this->enableCurrencyFeature) {
                foreach ($this->service_currencies as $serviceId => $currencyId) {
                    Service::where('id', $serviceId)->update(['currency_id' => $currencyId ?: null]);
                }
            }
        });
        Flux::toast('Divisa cambiada éxitosamente.');
        $this->loadRateTableData();
        $this->dispatch('escape-enabled');
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
        $this->dispatch('escape-enabled');
    }

    #[On('editCountry')]
    public function editCountry($dict)
    {
        $dict = (array)json_decode($dict);
        $country_name = $dict['country_name'] ?? null;
        $colIndex = $dict['colIndex'] ?? null;

        if (empty($country_name) || !isset($this->table_columns[$colIndex])) {
            Flux::toast(variant: 'error', text: 'Datos inválidos');
            return false;
        }

        $origin_id = $this->table_columns[$colIndex]['origin_id'] ?? null;
        $success = false;

        try {
            $success = DB::transaction(function () use ($country_name, $colIndex, $origin_id) {
                $existingOrigin = Origin::where('name', $country_name)
                                    ->where('id', '!=', $origin_id)
                                    ->first();

                if ($existingOrigin) {
                    $conflictingServices = Service::where('origin_id', $origin_id)
                        ->whereHas('serviceType', function($query) use ($existingOrigin) {
                            $query->whereExists(function($subQuery) use ($existingOrigin) {
                                $subQuery->select('id')
                                    ->from('services')
                                    ->whereColumn('service_type_id', 'service_types.id')
                                    ->where('origin_id', $existingOrigin->id);
                            });
                        })
                        ->count();

                    if ($conflictingServices > 0) {
                        Flux::toast(
                            variant: 'warning', 
                            text: 'El cambio crearía '.$conflictingServices.' combinación(es) duplicada(s)',
                            heading: 'Error de duplicado'
                        );

                        return false;
                    }
                }

                $this->table_columns[$colIndex]['label'] = $country_name;

                foreach ($this->table_columns as $column) {
                    if (isset($column['origin_id'])) {
                        Origin::where('id', $column['origin_id'])
                            ->update(['name' => $column['label']]);
                    }
                }

                return true;
            });
        } catch (\Exception $e) {
            Flux::toast(
                variant: 'error', 
                text: 'Error al procesar los cambios: '.$e->getMessage(),
                heading: 'Error'
            );
            return false;
        }finally{
            if ($success) {
                Flux::toast('Edición de país realizada éxitosamente.');
                $this->loadRateTableData();
                $this->dispatch('escape-enabled');
            }
            Flux::modal('dichotomic-modal')->close();
        }

        return $success;
    }

    public function addRowPercentage(){
        $variables_to_validate = [
            'numericValueTariff',
        ];

        try {
            $this->validateLivewireInput($variables_to_validate);
        } catch (\Exception $e) {
            $this->dispatch('x-unblock-loading-percentage-modal');
            $this->dispatch('escape-enabled');
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
            $this->dispatch('escape-enabled');
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
            $this->dispatch('escape-enabled');
            $this->validateLivewireInput($variables_to_validate);
        }

        $formattedValue = $this->numericValueTariff + 0;

        $label = $this->selectedOperator . $formattedValue;

        if ((is_numeric($formattedValue) && $formattedValue <= 0) && ($this->selectedOperator == '<' || $this->selectedOperator == '<=')) {
            $this->dispatch('escape-enabled');
            $this->addError('numericValueTariff', 'No se pueden validar rangos menores o iguales a cero.');
            return;
        }

        try {
            Validator::make(
                ['label' => $label, 'service_type_id' => $this->serviceTypeId],
                [
                    'label' => [
                        Rule::unique('weight_tiers')
                            ->where(fn ($query) => $query->where('service_type_id', $this->serviceTypeId))
                    ]
                ],
                [
                    'label.unique' => 'La tarifa "' . $label . '" ya existe para este maestro.'
                ]
            )->validate();
        } catch (\Exception $e) {
            $this->dispatch('escape-enabled');
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
            // 'max_weight' => ($lastTier->max_weight ?? 0) + 1000,
            'display_order' => ($lastTier->display_order ?? 0) + 1,
            'service_type_id' => $this->serviceTypeId, // <--- AGREGAR ESTO
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
        
        $operationSuccess = DB::transaction(function () {
            $origin = Origin::firstOrCreate(['name' => $this->newColumnName]);
            
            if (Service::where('origin_id', $origin->id)
                    ->where('service_type_id', $this->serviceTypeId)
                    ->exists()) {
                Flux::toast(
                    variant: 'warning', 
                    text: 'Ya existe un servicio con esta combinación de país (' . $this->newColumnName . ') y tipo (' . $this->serviceTypeName . ').', 
                    heading: 'Error'
                );
                return false;
            }
            
            Service::create([
                'origin_id' => $origin->id,
                'service_type_id' => $this->serviceTypeId ,
                'minimum_charge' => 0,
                'currency_id' => null
            ]);
            
            return true;
        });

        if (!$operationSuccess) {
            return;
        }

        $this->reset(['newColumnName']);
        Flux::toast('Combinación agregada correctamente.', 'Éxito');
        $this->loadRateTableData();
    }

    #[On('removeColumn')]
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
                if (!Service::where('service_type_id', $this->serviceTypeId)->exists()) {
                    foreach ($this->rows_data as $row) {
                        if (!is_null($row['tier_id'])) {
                            WeightTier::destroy($row['tier_id']);
                        }
                    }
                }
            }
        });
        $this->loadRateTableData();
        Flux::toast('País eliminado correctamente.', 'Éxito');
        Flux::modal('dichotomic-modal')->close();
    }

    #[On('removeRow')]
    public function removeRow($tierId)
    {
        WeightTier::destroy($tierId);
        Flux::toast('Tarifa eliminada correctamente.', 'Éxito');
        Flux::modal('dichotomic-modal')->close();
        $this->loadRateTableData();
    }


    public function render()
    {
        return view('livewire.menu.index-menu-component');
    }
}
