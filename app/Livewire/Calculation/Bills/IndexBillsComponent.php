<?php

namespace App\Livewire\Calculation\Bills;

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
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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

    public $groupedItems = [];
    
    #[Validate('required', message: 'VALIDACIÓN: Debe seleccionar una etapa')]
    #[Validate('in:Origen,Destino', message: 'VALIDACIÓN: La etapa debe ser Origen o Destino')]
    public string $newStage = 'Destino';
    #[Validate('required', message: 'VALIDACIÓN: Debe ingresar un concepto')]
    #[Validate('min:3', message: 'VALIDACIÓN: Debe ingresar mínimo 3 caracteres')]
    // #[Validate('unique:cost_items,concept', message: 'VALIDACIÓN: Este concepto ya existe')]
    public string $newConcept = '';


    #[On('set-cost-item-from-calculation')]
    public function SetCostItemFromCalculationVariables($dict)
    {
        // 1. Obtenemos el índice del array y la nueva configuración de la fórmula.
        $itemIndex = $dict['idx_item'];
        $newFormulaLogic = $dict['logic']; // La clave es 'formula' según tu método save() del modal

        // 2. Obtenemos el ID del item de la base de datos a partir del array local.
        // Es importante verificar que el item exista para evitar errores.
        if (isset($this->costItems[$itemIndex]['id'])) {
            $itemId = $this->costItems[$itemIndex]['id'];

            // 3. Actualizamos el array local para que la vista se refresque al instante.
            $this->costItems[$itemIndex]['formula'] = $newFormulaLogic;

            // 4. Buscamos el modelo en la BD y guardamos el cambio permanentemente.
            $item = CostItem::find($itemId);
            if ($item) {
                $item->update([
                    'formula' => $newFormulaLogic
                ]);
            }
            
            // 5. (Opcional pero recomendado) Muestra una notificación de éxito.
            Flux::toast('Fórmula guardada exitosamente.');
        }
    }

    public function mount()
    {
        // $el = new ExpressionLanguage();
        // $formula = 'max(monto * 0.0025, 348900)';
        // $resultado = $el->evaluate($formula, ['monto' => 500000]);
        $this->SelectMasterTypeService($this->serviceTypeName, true);

        $this->currencies = Currency::all();
        $this->loadCostItems();
        $this->groupedItems = collect($this->costItems)->groupBy('stage')->toArray();
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

        $logic = $this->costItems[$idx]['formula'];
        $mediator_dict = [
            'idx_item' => $idx,
            'logic' => $logic
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
        $this->groupedItems = collect($this->costItems)->groupBy('stage')->toArray();
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

        $this->newConcept = trim($this->newConcept);

        try {
            $this->validateLivewireInput($variables_to_validate);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('confirm-validation-modal', $e->getMessage());
            foreach ($e->validator->errors()->getMessages() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
            return;
        }

        try {
            Validator::make(
                ['concept' => $this->newConcept],
                [
                    'concept' => Rule::unique('cost_items', 'concept')
                        ->where('service_type_id', $this->serviceTypeId)
                ],
                [
                    'concept.unique' => 'VALIDACIÓN: Este concepto ya existe para el tipo de gasto seleccionado.',
                ]
            )->validate();

        } catch (\Exception $e) {
            $this->dispatch('escape-enabled');
            $this->dispatch('confirm-validation-modal', $e->getMessage());
            $this->addError('newConcept', $e->getMessage());
            return;
        }
        
        CostItem::create([
            'service_type_id' => $this->serviceTypeId,
            'stage' => ucfirst(trim($this->newStage)),
            'concept' => $this->newConcept,
        ]);

        $this->reset(['newConcept', 'newStage']);
        $this->dispatch('escape-enabled');
        $this->loadCostItems();
    }

    #[On('removeItem')]
    public function removeItem($itemId)
    {
        CostItem::destroy($itemId);
        $this->loadCostItems();
        Flux::modal('dichotomic-modal')->close();
        $this->dispatch('escape-enabled');
        Flux::toast('Concepto eliminado correctamente.', 'Éxito');
    }

    public function openEditItemConcept($concept, $stage, $id, $idx){
        $sub_dict = [
            'costItem_id' => $id,
            'costItem_idx' => $idx,
            'stage' => $stage,
        ];
    
        $mediator_dict = [
            'sub_dict' => $sub_dict,
            'updated' => 'mediator-obsto-index-bills-comp-costitems',
            'title' => 'Editar Nombre Concepto',
            'value' => $concept,
        ];


        $this->dispatch('mediator-mount-observations-modal', $mediator_dict);
    }

    #[On('obsto-index-bills-comp-costitems')]
    public function ItemDataObstoMainCompVariables($dict){
        $itemId = $dict['costItem_id'];
        $idx = $dict['costItem_idx'];
        $stage = $dict['stage'];
        $value = $dict['value'];
        
        $item = CostItem::find($itemId);
        try {

            Validator::make(
                ['concept' => $value],
                [
                    'concept' => Rule::unique('cost_items', 'concept')
                        ->where('service_type_id', $this->serviceTypeId)
                        ->ignore($itemId)
                ],
                [
                    'concept.unique' => 'VALIDACIÓN: Este concepto ya existe para el tipo de gasto seleccionado.',
                ]
            )->validate();

            $item->concept = $value;
            $item->save();

            $this->groupedItems[$stage][$idx]['concept'] = $value;
            
            Flux::toast('Concepto actualizado correctamente.', 'Éxito');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->addError('concept', $e->getMessage()); 
            $this->dispatch('confirm-validation-modal', $e->getMessage());
        }
        finally {
            $this->dispatch('escape-enabled');
        }
    }

    public function render()
    {
        return view('livewire.calculation.bills.index-bills-component');
    }
}