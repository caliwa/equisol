<?php

namespace App\Livewire\Bills;

use Flux\Flux;
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

    public $modalConfirmValidationMessage;

    #[On('confirm-validation-modal')]
    public function MediatorConfirmValidationModal($aux_conf_val_modal){
        $this->modalConfirmValidationMessage = $aux_conf_val_modal;
        Flux::modal('confirm-validation-modal')->show();
        $this->dispatch('escape-enabled');
    }

    #[On('mediator-mount-dichotomic-asking-modal')]
    public function MediatorDichotomicAskingModal($value){
        $this->dispatch('mount-dichotomic-asking-modal', $value);
    }

    #[On('mediator-calculation-strategy-modal')]
    public function MediatorCalculationStrategyModal($dict){
        $dict['zIndexModal'] = $this->convertStackCountPlusOne();
        $this->dispatch('mount-calculation-strategy-modal', $dict);
    }

    //
    #[On('mediator-calculation-to-index-bills')]
    public function MediatorCalculationToIndexBills($dict){
        $this->dispatch('set-cost-item-from-calculation', $dict);
    }

    public function placeholder(){
        return view('livewire.placeholder.index-menu-placeholder');
    }

    public function render()
    {
        return view('livewire.bills.mediator-bills-component');
    }
}
