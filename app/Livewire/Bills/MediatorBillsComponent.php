<?php

namespace App\Livewire\Bills;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;
use App\Livewire\Traits\ModalEnableTrait;
use App\Livewire\Traits\EscapeEnableTrait;
use App\Livewire\Traits\ProcessingEscapeTrait;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;

#[Lazy]
class MediatorBillsComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        ModalEnableTrait,
        // EscapeEnableTrait,
        ProcessingEscapeTrait;

    #[On('confirm-validation-modal')]
    public function MediatorConfirmValidationModal($aux_conf_val_modal){
        $this->dispatch('mount-confirm-validation', $aux_conf_val_modal);
    }

    #[On('mediator-mount-dichotomic-asking-modal')]
    public function MediatorDichotomicAskingModal($value){
        $this->dispatch('mount-dichotomic-asking-modal', $value);
    }

    public function placeholder(){
        return view('livewire.placeholder.index-menu-placeholder');
    }

    public function render()
    {
        return view('livewire.bills.mediator-bills-component');
    }
}
