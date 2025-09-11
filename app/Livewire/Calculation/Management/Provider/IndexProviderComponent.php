<?php

namespace App\Livewire\Calculation\Management\Provider;

use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\RateProvider;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;

#[Isolate]
class IndexProviderComponent extends Component
{
    use AdapterValidateLivewireInputTrait;

    public $providers;

    #[Validate('required', message: 'VALIDACIÓN: Debe ingresar un nombre')]
    #[Validate('unique:rate_providers,name', message: 'VALIDACIÓN: El nombre ya está en uso')]
    public $name = '';
    #[Validate('required', message: 'VALIDACIÓN: Debe ingresar un código')]
    #[Validate('unique:rate_providers,code', message: 'VALIDACIÓN: El código ya está en uso')]
    #[Validate('alpha_dash', message: 'VALIDACIÓN: El código solo puede contener letras, números, guiones y guiones bajos')]
    public $code = '';

    public function mount()
    {
        $this->loadProviders();
    }

    public function BackToMastersView()
    {
        $this->redirectRoute('masters', navigate:true);
    }

    public function loadProviders()
    {
        $this->providers = RateProvider::all()->toArray();
    }

    public function addProvider()
    {
        $variables_to_validate = [
            'name',
            'code',
        ];

        try {
            $this->validateLivewireInput($variables_to_validate);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('confirm-validation-modal', $e->getMessage());

            foreach ($e->validator->errors()->getMessages() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
            return;
        }

        RateProvider::create([
            'name' => $this->name,
            'code' => strtolower($this->code),
        ]);

        $this->reset(['name', 'code']);
        $this->loadProviders();
        Flux::toast('Proveedor agregado éxitosamente.');
        $this->dispatch('escape-enabled');
    }
    
    #[On('deleteProvider')]
    public function deleteProvider($id)
    {
        RateProvider::find($id)->delete();
        $this->loadProviders();
        Flux::toast('Proveedor eliminado correctamente.', 'Éxito');
        Flux::modal('dichotomic-modal')->close();
    }

    public function openEditItemName($name, $id, $idx){
        $sub_dict = [
            'provider_id' => $id,
            'provider_idx' => $idx,
        ];
    
        $mediator_dict = [
            'sub_dict' => $sub_dict,
            'updated' => 'mediator-obsto-index-provider-comp-provider',
            'title' => 'Editar Nombre proveedor',
            'value' => $name,
        ];

        $this->dispatch('mediator-mount-observations-modal', $mediator_dict);
    }

    #[On('obsto-index-provider-comp-provider')]
    public function ObsToIndexProviderComp($dict){
        $itemId = $dict['provider_id'];
        $idx = $dict['provider_idx'];
        $value = $dict['value'];
        
        $item = RateProvider::find($itemId);
        try {

            Validator::make(
                ['name' => $value],
                [
                    'name' => Rule::unique('rate_providers', 'name')
                        ->ignore($itemId)
                ],
                [
                    'name.unique' => 'VALIDACIÓN: Este nombre ya lo está asignado a un proveedor.',
                ]
            )->validate();

            $item->name = $value;
            $item->save();

            $this->providers[$idx]['name'] = $value;

            Flux::toast('Nombre actualizado correctamente.', 'Éxito');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->addError('name', $e->getMessage());
            $this->dispatch('confirm-validation-modal', $e->getMessage());
        }
        finally {
            $this->dispatch('escape-enabled');
        }
    }

    public function render()
    {
        return view('livewire.calculation.management.provider.index-provider-component');
    }
}
