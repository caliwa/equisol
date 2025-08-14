<?php

namespace App\Livewire\Validation;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Isolate;
use App\Livewire\Traits\CloseModalClickTrait;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;

#[Isolate]
class ConfirmValidationModalComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        AdapterValidateLivewireInputTrait,
        CloseModalClickTrait;

    public $errorMsgConfirmationModal = 'Cargando...';

    public $isVisibleConfirmValidationModal = false;

    #[On('isVisibleConfirmValidationModal')]
    public function setModalVariable($value){
        $this->ResetModalVariables();
        $this->isVisibleConfirmValidationModal = $value;
    }

    #[On('mount-confirm-validation')]
    public function mount_artificially($errorMsgConfirmationModal){
        if ($errorMsgConfirmationModal) {
            if (preg_match('/\(and (\d+) more error(s?)\)$/', $errorMsgConfirmationModal, $matches)) {
                $errorCount = $matches[1];
                
                $newEnding = $errorCount == 1 ? "falta otra validación" : "faltan otras $errorCount validaciones";
                
                $this->errorMsgConfirmationModal = preg_replace('/\(and \d+ more error(s?)\)$/', "($newEnding)", $errorMsgConfirmationModal);
            } else {
                $this->errorMsgConfirmationModal = $errorMsgConfirmationModal;
            }
        }
        $this->isVisibleConfirmValidationModal = true;
        $this->dispatch('escape-enabled');
    }

    public function ResetModalVariables(){
        $this->reset(array_keys($this->all()));
    }

    public function render()
    {
        return view('livewire.validation.confirm-validation-modal-component');
    }
}
