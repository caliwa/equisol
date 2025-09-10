<?php

namespace App\Livewire\Calculation\Currency;

use Flux\Flux;
use Livewire\Component;
use App\Models\Currency;

use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Isolate;
use App\Livewire\Traits\CloseModalClickTrait;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;

#[Isolate]
class CurrencyManagerComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        AdapterValidateLivewireInputTrait,
        CloseModalClickTrait;

    public $currencies;
    public $currency_id;
    public $code, $name, $value;
    public $isVisibleCurrencyManagerComponent = false;
    public $isEditing = false;

    #[On('mount-open-currency-manager')]
    public function mount_artificially()
    {
        $this->resetInputFields();
        $this->loadCurrencies();
        $this->isVisibleCurrencyManagerComponent = true;
        $this->dispatch('escape-enabled');
    }

    public function loadCurrencies()
    {
        $this->currencies = Currency::orderBy('name')->get();
    }

    public function resetInputFields()
    {
        $this->reset(['currency_id', 'code', 'name', 'value', 'isEditing']);
        $this->resetErrorBag();
    }

    public function create()
    {
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $currency = Currency::findOrFail($id);
        $this->currency_id = $id;
        $this->code = $currency->code;
        $this->name = $currency->name;
        $this->value = $currency->value;
        $this->isEditing = true;
    }

    public function save()
    {
        try {
            $rules = [
                'code' => [
                    'required',
                    'string',
                    'size:3',
                    // Regla de unicidad: ignora el registro actual al editar
                    Rule::unique('currencies_master')->ignore($this->currency_id),
                ],
                'name' => 'required|string|max:255',
                'value' => 'required|numeric|min:0',
            ];

            $messages = [
                'code.required' => 'El código es obligatorio.',
                'code.size' => 'El código debe tener 3 caracteres.',
                'code.unique' => 'Este código ya está en uso.',
                'name.required' => 'El nombre es obligatorio.',
                'value.required' => 'El valor es obligatorio.',
                'value.numeric' => 'El valor debe ser un número.',
            ];

            $this->validate($rules, $messages);

        } catch (\Illuminate\Validation\ValidationException $e) {
            foreach ($e->validator->errors()->getMessages() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }

            $this->dispatch('escape-enabled');
            return;
        }

        Currency::updateOrCreate(
            ['id' => $this->currency_id],
            [
                'code' => strtoupper($this->code),
                'name' => $this->name,
                'value' => $this->value,
            ]
        );

        Flux::toast(
            $this->isEditing ? '¡Moneda actualizada con éxito!' : '¡Moneda creada con éxito!',
            'Éxito'
        );

        $this->resetInputFields();
        $this->loadCurrencies();
        $this->dispatch('escape-enabled');
    }
    
    #[On('deleteCurrency')]
    public function deleteCurrency($id)
    {
        // 1. Cargar la moneda con el CONTEO de sus relaciones para ser eficientes.
        $currency = Currency::withCount(['services', 'costItems'])->find($id);

        // Si por alguna razón no se encuentra la moneda, no hacemos nada.
        if (!$currency) {
            return;
        }
        if ($currency->services_count > 0 || $currency->cost_items_count > 0) {
            
            $usages = [];
            if ($currency->services_count > 0) {
                $usages[] = $currency->services_count . ' servicio(s)';
            }
            if ($currency->cost_items_count > 0) {
                $usages[] = $currency->cost_items_count . ' item(s) de costo';
            }
            
            $msg = 'La moneda "' . $currency->name . '" no se puede eliminar porque está asociada a ' . implode(' y ', $usages) . '.';

            $this->dispatch('confirm-validation-modal', $msg);
            $this->dispatch('escape-enabled');
            Flux::modal('dichotomic-modal')->close();

        } else {
            // 5. Si no está en uso, proceder con la eliminación.
            $currency->delete();
            $this->loadCurrencies();
            Flux::toast('¡Moneda eliminada con éxito!', 'Éxito');
        }
    }


    public function ResetModalVariables(){
        $this->resetErrorBag();
        $this->reset(array_keys($this->all()));
    }

    public function render()
    {
        return view('livewire.calculation.currency.currency-manager-component');
    }
}
