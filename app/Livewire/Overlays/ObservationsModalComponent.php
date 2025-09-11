<?php

namespace App\Livewire\Overlays;

use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Validate;
use App\Livewire\Traits\CloseModalClickTrait;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;

#[Isolate]
class ObservationsModalComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        AdapterValidateLivewireInputTrait,
        CloseModalClickTrait;

    public $isVisibleObservationsModal = false;

    #[Validate('required', message: 'VALIDACIÓN: Debe haber mínimo un caracter')]
    public $observations_data;

    public $updated_element;

    public $title_presentation;

    public $dict_to_dispatch;

    #[On('isVisibleObservationsModal')]
    public function setModalVariable($value){
        $this->ResetModalVariables();
        $this->isVisibleObservationsModal = $value;
    }

    #[On('mount-observations-modal')]
    public function mount_artificially($dict){
        $this->zIndexModal = $dict['zIndexModal'];

        $this->observations_data = $dict['value'];

        $this->dict_to_dispatch = $dict['sub_dict'];

        $this->updated_element = $dict['updated'];

        $this->title_presentation = $dict['title'];

        $this->isVisibleObservationsModal = true;
        $this->dispatch('escape-enabled');
    }


    public function save()
    {
        $variables_validate = [
            'observations_data',
        ];

        try {
            $this->validateLivewireInput($variables_validate);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('confirm-validation-modal', $e->getMessage());

            foreach ($e->validator->errors()->getMessages() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
            return;
        }
        $this->dict_to_dispatch['value'] = $this->observations_data;
        $this->dispatch($this->updated_element, $this->dict_to_dispatch);
        $this->dispatch('MediatorSetModalFalse', 'isVisibleObservationsModal');
    }

    public function ResetModalVariables(){
        $this->reset(array_keys($this->all()));
    }

    public function render()
    {
        return view('livewire.overlays.observations-modal-component');
    }
}
