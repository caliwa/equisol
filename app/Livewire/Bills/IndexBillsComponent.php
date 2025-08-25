<?php

namespace App\Livewire\Bills;

use Flux\Flux;
use App\Models\Service;
use Livewire\Component;
use App\Models\CostItem;
use App\Models\Currency;
use Livewire\Attributes\On;
use App\Models\CostServiceType;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Validate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

#[Isolate]
class IndexBillsComponent extends Component
{
    use AdapterValidateLivewireInputTrait;

    public int $serviceTypeId;
    public string $serviceTypeName = 'Gastos Mar';
    public string $type_service = 'g_mar';

    public $costItems;
    public $currencies;
    
    #[Validate('required', message: 'VALIDACIÓN: Debe seleccionar una etapa')]
    #[Validate('in:Origen,Destino', message: 'VALIDACIÓN: La etapa debe ser Origen o Destino')]
    public string $newStage = 'Destino';
    #[Validate('required', message: 'VALIDACIÓN: Debe ingresar un concepto')]
    #[Validate('min:3', message: 'VALIDACIÓN: Debe ingresar mínimo 3 caracteres')]
    #[Validate('unique:cost_items,concept', message: 'VALIDACIÓN: Este concepto ya existe')]
    public string $newConcept = '';


    #[On('set-cost-item-from-calculation')]
    public function SetCostItemFromCalculationVariables($dict){
        // dd($this->costItems[$dict['idx_item']]['formula']);
        // dd($this->costItems[$dict['idx_item']]['formula']);
        $this->costItems[$dict['idx_item']]['formula'] = $dict['expression'];
    }

    public function mount()
    {
        // $el = new ExpressionLanguage();
        // $formula = 'max(monto * 0.0025, 348900)';
        // $resultado = $el->evaluate($formula, ['monto' => 500000]);
        $this->SelectMasterTypeService($this->serviceTypeName, true);

        $this->currencies = Currency::all();
        $this->loadCostItems();
    }

    public function saveNewCurrencyMaster($idx){
        DB::transaction(function () {
            // foreach ($this->service_currencies as $serviceId => $currencyId) {
            //     CostServiceType::where('id', $serviceId)->update(['currency_id' => $currencyId ?: null]);
            // }
        });
        Flux::toast('Divisa cambiada éxitosamente.');
        $this->dispatch('escape-enabled');
    }


    function SelectMasterTypeService($serviceTypeName, $firstTime = null)
    {
        $this->serviceTypeName = $serviceTypeName;
        $serviceType = CostServiceType::where('name', $serviceTypeName)->first();
        $this->serviceTypeId = $serviceType->id;
        $this->loadCostItems();
        if(is_null($firstTime)){
            Flux::toast('Maestro cambiado éxitosamente.');
        }
        $this->dispatch('escape-enabled');
    }

    public function openCalculationStrategyModal($idx){
        $mediator_dict = [
            'idx_item' => $idx,
        ];

        $this->dispatch('mediator-calculation-strategy-modal', $mediator_dict);
    }

    public function loadCostItems()
    {
        $this->costItems = CostItem::where('service_type_id', $this->serviceTypeId)
            ->orderBy('stage')
            ->orderBy('concept')
            ->get()
            ->toArray();
    }

    public function updatedCostItems($value, $key)
    {
        $parts = explode('.', $key);
        $itemId = $this->costItems[$parts[0]]['id'];
        $field = $parts[1];

        $item = CostItem::find($itemId);
        if ($item) {
            $item->{$field} = $value === '' ? null : $value;
            $item->save();
        }
    }

    public function addNewItem()
    {
        $variables_to_validate = [
            'newStage',
            'newConcept',
        ];

        try {
            $this->validateLivewireInput($variables_to_validate);
        } catch (\Exception $e) {
            $this->dispatch('confirm-validation-modal', $e->getMessage());

            // Flux::modal('confirm-validation-modal')->show();
            // $this->modalConfirmValidationMessage = $e->getMessage();

            // $this->dispatch('escape-enabled');
            $this->validateLivewireInput($variables_to_validate);
            return;
        }
        
        CostItem::create([
            'service_type_id' => $this->serviceTypeId,
            'stage' => $this->newStage,
            'concept' => $this->newConcept,
        ]);

        $this->reset(['newConcept', 'newStage']);
        $this->dispatch('escape-enabled');
        $this->loadCostItems();
    }
    
    public function removeItem($itemId)
    {
        CostItem::destroy($itemId);
        $this->loadCostItems();
    }

    public function render()
    {
        $groupedItems = collect($this->costItems)->groupBy('stage');
        return view('livewire.bills.index-bills-component', [
            'groupedItems' => $groupedItems,
        ]);
    }
}