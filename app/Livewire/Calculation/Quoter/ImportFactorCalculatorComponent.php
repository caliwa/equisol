<?php

namespace App\Livewire\Calculation\Quoter;

use Flux\Flux;
use App\Models\Origin;

use Livewire\Component;

use Nnjeim\World\World;
use App\Models\CostItem;
use Livewire\Attributes\On;
use App\Models\ProviderRate;
use App\Models\ProviderZone;
use App\Models\RateProvider;
use App\Models\CostServiceType;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Validate;
use App\Services\RuleEngineService;
use App\Livewire\Traits\GetFlagEmojiTrait;


use App\Livewire\Traits\CalculateMasterRateTrait;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;
use App\Livewire\Traits\CleanInputMaskingTrait;
use App\Models\Currency;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;


#[Isolate]
class ImportFactorCalculatorComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        AdapterValidateLivewireInputTrait,
        CleanInputMaskingTrait,
        GetFlagEmojiTrait,
        CalculateMasterRateTrait;

    // Propiedades para "Cambio"
    public $trm;
    public $eur_usd;

    // Propiedades para "Transporte"
    // public $transporte = 'courrier';
    public $tiempo_transporte = 4;

    #[Validate('required', message: 'VALIDACIÓN: Debe seleccionar un país de origen')]
    public $origin;
    #[Validate('required', message: 'VALIDACIÓN: Debe seleccionar un modo de transporte')]
    public $transport_mode;
    #[Validate('required', message: 'VALIDACIÓN: Debe ingresar un peso')]
    #[Validate('numeric', message: 'VALIDACIÓN: El peso debe ser un número')]
    // #[Validate('max:50', message: 'VALIDACIÓN: El peso no debe superar los 50 kgs')] // <--- ELIMINA ESTA LÍNEA
    #[Validate('min:0.1', message: 'VALIDACIÓN: El peso debe ser mayor a 0 kgs')]
    public $weight = 0;

    #[Validate('required', message: 'VALIDACIÓN: Debe ingresar un arancel')]
    public $tariff = 10;
    
    #[Validate('required', message: 'VALIDACIÓN: Debe ingresar un costo')]
    #[Validate('numeric', message: 'VALIDACIÓN: El costo debe ser numérico')]
    // #[Validate('max:2000', message: 'VALIDACIÓN: El costo no debe superar los 2000')] // <--- ELIMINA ESTA LÍNEA
    #[Validate('min:0.1', message: 'VALIDACIÓN: El costo debe ser mayor a 0')]
    public $cost = 0;

    // Propiedades para "Costos"
    public $moneda_costo = 'usd';
    public $multas = '';
    
    public $origins_countries = [];

    public $countries;

    public $cost_show = 0;
    public $origin_cost_show = 0;
    public $freight_show = 0;
    public $insurance_show = 0;
    public $cif_show = 0;
    public $tariff_show = 0;
    public $destination_costs_show = 0;

    public $ddp_cost = 0;
    public $import_factor = 0;

    public $transit_days = 0;

    public $nextIdxArray = 1;

    public $variables_pallet = [];

    public $selectedCountry;


    public function mount(){
        $eur_trm = floatval(Currency::where('code', 'EUR')->first()->value);
        $usd_trm = floatval(Currency::where('code', 'USD')->first()->value);

        $this->eur_usd = floatval(number_format($eur_trm / $usd_trm, 2, '.', ''));
        $this->trm = $usd_trm;
      // 1. Obtener tus países de origen como una Colección de Laravel.
        // Es mejor trabajar con colecciones que con arrays directamente.
        $this->origins_countries = Origin::all();

        // 2. Obtener todos los países del mundo desde el paquete.
        $action = World::setLocale('es')->countries(['fields' => 'iso2,name']);

        if ($action->success) {

            $countryIsoMap = collect($action->data)->pluck('iso2', 'name');

            $this->origins_countries = $this->origins_countries->map(function ($originCountry) use ($countryIsoMap) {

                $originCountry->iso2 = $countryIsoMap[$originCountry->name] ?? null;

                return $originCountry;

            })->toArray();
        }

        $this->variables_pallet[] = $this->Get_InitPallet();
        
    }

    protected function getChargeableWeight(float $realWeight, float $totalVolume): float
    {
        $volumetricWeight = 0;

        switch ($this->transport_mode) {
            case 'aerial':
                // Factor aéreo estándar: 1 m³ = 167 kg
                $factor = 167;
                $volumetricWeight = $totalVolume * $factor;
                break;

            case 'maritime':
                // Factor marítimo LCL estándar: 1 m³ = 1000 kg (1 Tonelada)
                $factor = 1000;
                $volumetricWeight = $totalVolume * $factor;
                break;
            
            case 'courrier':
                $factor = 200;
                $volumetricWeight = $totalVolume * $factor;
                break;
        }

        // La regla de oro: se cobra el valor que sea MAYOR.
        return max($realWeight, $volumetricWeight);
    }

    public function AddValueInputVariables(){
        $this->resetErrorBag();
        if($this->nextIdxArray == 10){
            $this->dispatch('confirm-validation-modal', 'No pueden agregar más de 10 pallets.');
            $this->dispatch('escape-enabled');
            return;
        }
        array_push($this->variables_pallet, $this->nextIdxArray);
        $this->variables_pallet[count($this->variables_pallet) - 1] = $this->Get_InitPallet();
        $this->nextIdxArray += 1;

        $this->dispatch('escape-enabled');
    }

    public function RemoveInputVariables($idx){
        if($this->nextIdxArray == 1){
            Flux::toast('No puede eliminar el primer registro.');
            $this->dispatch('escape-enabled');
            return;
        }
        $this->nextIdxArray -= 1;

        $this->ResetShowValues();

        unset($this->variables_pallet[$idx]);

        $this->variables_pallet = array_values($this->variables_pallet);

        $this->CalculateArrayPallet();

        $this->dispatch('escape-enabled');

    }

    public function Get_InitPallet(){
        return ['width'=>0,'length'=>0,'height'=>0];
    }
    
    public function calculateBtn(){

        $this->cost = $this->CleanInputMasking($this->cost);
        $this->weight = $this->CleanInputMasking($this->weight);

        $variables_to_validate = [
            'transport_mode',
            'origin',
            'tariff',
            'weight',
            'cost'
        ];

        try {
            $this->validateLivewireInput($variables_to_validate);

            if ($this->transport_mode === 'courrier') {

                $this->validate([
                    'weight' => 'numeric|max:50',
                    'cost'   => 'numeric|max:2000',
                ], [
                    'weight.max' => 'VALIDACIÓN: El peso no debe superar los 50 kgs.',
                    'cost.max'   => 'VALIDACIÓN: El costo no debe superar los 2000.',
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('escape-enabled');
            foreach ($e->validator->errors()->getMessages() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
            $this->dispatch('confirm-validation-modal', $e->getMessage());

            return;
        }

        $this->ValidateAllPallets();

        $this->ResetShowValues();

        $this->cost_show = $this->cost;
        if($this->transport_mode == 'maritime'){
            $this->CalculateMaritimeImportFactor();
        }else if ($this->transport_mode == 'aerial'){
            $this->CalculateAerialImportFactor();
        }else if ($this->transport_mode == 'courrier'){
            $this->CalculateCourrierImportFactor();
        }

        $this->CalculateTransitDays($this->origin, $this->transport_mode);

        Flux::toast('Cálculo realizado exitosamente.', 'Cotizador F.I');

        $this->isCalculated = true;
    }
    public $isCalculated = false;


    public function ResetShowValues(){
        if($this->isCalculated){
            $this->reset(['isCalculated']);
            Flux::toast('Los valores se han reiniciado, por favor vuelva a calcular.', 'Cotizador F.I', duration: 5000);

            $this->reset(['cost_show', 'origin_cost_show', 'freight_show', 
                'insurance_show', 'cif_show', 'tariff_show', 
                'destination_costs_show', 'ddp_cost', 'import_factor', 'transit_days']);
        }

    }

    public function updatedRole(){
        $this->ResetShowValues();
    }

    public function updatedOrigin(){
        $this->ResetShowValues();
    }

    protected function ValidatePallet($fieldName)
    {
        $rule = 'required|numeric|min:1';
        $messages = [
            $fieldName . '.required' => 'El valor del pallet es requerido, no puede estar vacío.',
            $fieldName . '.numeric'  => 'Este campo del pallet solo acepta valores numéricos.',
            $fieldName . '.min'      => 'El valor mínimo permitido en cada lado del pallet es 1.',
        ];

        if ($this->transport_mode === 'courrier') {
            $rule .= '|max:150';
            $messages[$fieldName . '.max'] = 'El valor máximo permitido en cada lado del pallet es 150 cm.'; // Añade el mensaje para 'max'
        }
        
        $this->validate([$fieldName => $rule], $messages);
    }

    public $total_volume = 0;

    public function CalculatePallet($idx, $field){
        $this->resetErrorBag();

        $fieldName = 'variables_pallet.' . $idx . '.' . $field;

        try{
            $this->ValidatePallet($fieldName);
            
            $this->CalculateArrayPallet();
        }catch(\Exception $e){
            $this->CalculateArrayPallet();
            Flux::toast($e->getMessage(), 'Validación');
            // $this->dispatch('confirm-validation-modal', $e->getMessage());
        }finally{
            $this->dispatch('escape-enabled');
        }

    }

    public function ValidateAllPallets(){
        $this->resetErrorBag();

        foreach ($this->variables_pallet as $idx => $pallet) {
            foreach (['width', 'length', 'height'] as $field) {
                $fieldName = 'variables_pallet.' . $idx . '.' . $field;
                $this->ValidatePallet($fieldName);
            }
        }

        // $this->CalculateArrayPallet();
        $this->dispatch('escape-enabled');
    }

    protected function CalculateArrayPallet(){
        $accumulator = 0;
        foreach ($this->variables_pallet as $pallet) {
    
            $width  = (float) ($pallet['width']  ?? 0);
            $length = (float) ($pallet['length'] ?? 0);
            $height = (float) ($pallet['height'] ?? 0);

            $accumulator += ($width / 100) * ($length / 100) * ($height / 100);
        }

        $this->total_volume = $accumulator;
    }

    public function setTransportMode($mode)
    {
        $this->ResetShowValues();
        $this->transport_mode = $mode;
    }

    public function CalculateMaritimeImportFactor(){
        // Calculamos el peso cobrable ANTES de buscar la tarifa
        $chargeableWeight = $this->getChargeableWeight($this->weight, $this->total_volume);
        
        $ratePickUpPrice = $this->calculateMasterRate('Pick Up Marítimo', $this->origin, $chargeableWeight, $this->trm, $this->eur_usd);
        $rateRecordPrice = $this->calculateMasterRate('Flete Marítimo', $this->origin, $chargeableWeight, $this->trm, $this->eur_usd);

        if (is_null($rateRecordPrice)) {
            $this->dispatch('confirm-validation-modal', 'No se encontró una tarifa de flete marítimo para el origen y peso especificados.');
            return;
        }
        
        $CIF = $this->cost + $rateRecordPrice + $ratePickUpPrice;
        $this->freight_show = $rateRecordPrice;
        $this->cif_show = $CIF;

        $serviceTypeName = "Gastos Mar";

        $CostItems =  $this->StructureCostItems($serviceTypeName);

        $variables_evaluate = [
            'CIF' => $CIF,
            'PESO' => $this->weight,
            'ARANCEL_MANUAL' => $this->tariff,
        ];

        $cost_items_value = $this->EvaluateCostItem($CostItems, $variables_evaluate, $this->trm, $this->eur_usd);

        $this->ddp_cost = ($cost_items_value + $CIF) - $this->cost;
        $this->import_factor = ($this->ddp_cost / $this->cost );
    }

    public function CalculateAerialImportFactor()
    {
        // Calculamos el peso cobrable ANTES de buscar la tarifa
        $chargeableWeight = $this->getChargeableWeight($this->weight, $this->total_volume);

        $ratePickUpPrice = $this->calculateMasterRate('Pick Up Aéreo', $this->origin, $chargeableWeight, $this->trm, $this->eur_usd);

        $rateRecordPrice = $this->calculateMasterRate('Flete Aéreo', $this->origin, $chargeableWeight, $this->trm, $this->eur_usd);

        if (is_null($rateRecordPrice)) {
            $this->dispatch('confirm-validation-modal', 'No se encontró una tarifa de flete aéreo para el origen y peso especificados.');
            return;
        }
        
        $CIF = $this->cost + $rateRecordPrice + $ratePickUpPrice;
        $this->freight_show = $rateRecordPrice;
        $this->cif_show = $CIF;
        // $this->tariff_show = $this->tariff;

        $serviceTypeName = "Gastos Aéreo";

        $CostItems =  $this->StructureCostItems($serviceTypeName);

        $variables_evaluate = [
            'CIF' => $CIF,
            'PESO' => $this->weight,
            'ARANCEL_MANUAL' => $this->tariff,
        ];

        $cost_items_value = $this->EvaluateCostItem($CostItems, $variables_evaluate, $this->trm, $this->eur_usd);

        $this->ddp_cost = ($cost_items_value + $CIF) - $this->cost;
        $this->import_factor = ($this->ddp_cost / $this->cost );
    }

        public function CalculateCourrierImportFactor(){    
        $rateRecordPrice = $this->CalculateCourierShippingCost();
        
        $CIF = $this->cost + $rateRecordPrice;
        $this->freight_show = $rateRecordPrice;
        $this->cif_show = $CIF;

        $serviceTypeName = "Gastos Courier";

        $CostItems =  $this->StructureCostItems($serviceTypeName);

        $variables_evaluate = [
            'CIF' => $CIF,
            'PESO' => $this->weight,
        ];

        $cost_items_value = $this->EvaluateCostItem($CostItems, $variables_evaluate, $this->trm, $this->eur_usd);

        $this->ddp_cost = ($cost_items_value + $CIF) - $this->cost;

        $this->import_factor = ($this->ddp_cost / $this->cost );
    }

    protected function StructureCostItems($serviceTypeName){
        $serviceType = CostServiceType::where('name', $serviceTypeName)->first();
        $cost_items = $this->loadCostItems($serviceType->id);
        $costItemsCollection = collect($cost_items);

        $CostItems = $costItemsCollection->sortBy(function ($item, $key) {
            switch ($item['stage']) {
                case 'Origen':
                    return 1;
                case 'Destino':
                    return 2;
                default:
                    return 3; 
            }
        });

        return $CostItems;
    }

    protected function EvaluateCostItem($CostItems, $variables_evaluate, $trm, $eur_usd){
        $count_formula = 0;
        $el = new ExpressionLanguage();
        $engine = new RuleEngineService();

        foreach ($CostItems as $idx => $item) {
            $evaluation = 0;
            $item_decoded = json_decode($item['formula'], true);
            $expression = $item_decoded['expression'];

            if($item_decoded['type'] == 'formula'){
                try {
                    $evaluation = $el->evaluate($expression, $variables_evaluate);
                } catch (\Exception $e) {
                    $testError = "Error en la fórmula: " . $e->getMessage();
                    $this->dispatch('confirm-validation-modal', $testError);
                    return;
                }

            }else if($item_decoded['type'] == 'rules'){
                $evaluation = $engine->process($expression, $variables_evaluate);
            }

            if($item['currency'] && $item['currency']['code'] == 'EUR'){
                $evaluation = $evaluation * $eur_usd;
            }
            if($item['currency'] && $item['currency']['code'] == 'COP'){
                $evaluation = $evaluation / $trm;
            }

            if($CostItems[$idx]['concept'] == 'Arancel'){
                $this->tariff_show = $evaluation;
            }
            if($CostItems[$idx]['concept'] == 'Seguro'){
                $this->insurance_show = $evaluation;
            }

            if($CostItems[$idx]['stage'] == 'Origen'){
                $this->origin_cost_show += $evaluation;
            }else if($CostItems[$idx]['stage'] == 'Destino'){
                $this->destination_costs_show += $evaluation;
            }

            $count_formula += $evaluation;
        }
        return $count_formula;
    }

    protected function CalculateCourierShippingCost(){
        $dhl = 1;

        $origin = $this->origin;

        $country = collect($this->origins_countries)->firstWhere('name', $origin);

        if ($country) {
            $selectedCountry_iso2 = $country['iso2'] ?? null;
        }

        $provider = RateProvider::find($dhl);
        $zoneRecord = ProviderZone::where('rate_provider_id', $provider->id)
            ->where('country_code', strtoupper($selectedCountry_iso2))
            ->first();

        if (!$zoneRecord) {
            $this->dispatch('confirm-validation-modal', 'País no encontrado para este proveedor');
            return;
        }

        $zone = $zoneRecord->zone;

        $chargeableWeight = $this->getChargeableWeight($this->weight, $this->total_volume);

        $rateRecordPrice = ProviderRate::where('rate_provider_id', $provider->id)
            ->where('zone', $zone)
            ->where('weight_kg', '>=', $chargeableWeight)
            ->orderBy('weight_kg', 'asc')
            ->first()
            ->price;
        

        if (!$rateRecordPrice) {
            $this->dispatch('confirm-validation-modal', 'El peso excede el límite de la tabla de tarifas');
            return;
        }

        return $rateRecordPrice;
    }

    public function CalculateTransitDays($originName, $modeName){
        $origin = Origin::with('transitModes')->where('name', $originName)->first();

        $days = null; 
        if ($origin) {
            $transitMode = $origin->transitModes->firstWhere('name', $modeName);
            
            if ($transitMode) {
                $days = $transitMode->pivot->days;
            }
        }
        $this->transit_days = $days ?? 0;
    }

//     protected function CalculateCourierShippingCost(){
    //     $dhl = 1;
    //     $provider = RateProvider::find($dhl);
    //     $zoneRecord = ProviderZone::where('rate_provider_id', $provider->id)
    //         ->where('country_code', strtoupper($this->selectedCountry))
    //         ->first();

    //     if (!$zoneRecord) {
    //         $this->dispatch('confirm-validation-modal', 'País no encontrado para este proveedor');
    //         return;
    //     }

    //     $zone = $zoneRecord->zone;

    //     $rateRecordPrice = ProviderRate::where('rate_provider_id', $provider->id)
    //         ->where('zone', $zone)
    //         ->where('weight_kg', '>=', $this->weight)
    //         ->orderBy('weight_kg', 'asc')
    //         ->first()
    //         ->price;
            
    //     if (!$rateRecordPrice) {
    //         $this->dispatch('confirm-validation-modal', 'El peso excede el límite de la tabla de tarifas');
    //         return;
    //     }

    //     return $rateRecordPrice;
    // }

    public function loadCostItems($serviceTypeId)
    {
        $res = CostItem::where('service_type_id', $serviceTypeId)
            ->with('currency:id,code')
            ->orderBy('stage')
            ->orderBy('concept')
            ->get()
            ->toArray();

        return $res;
    }

    public function render()
    {
        return view('livewire.calculation.quoter.import-factor-calculator-component');
    }
}