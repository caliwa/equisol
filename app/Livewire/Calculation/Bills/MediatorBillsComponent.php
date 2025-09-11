<?php

namespace App\Livewire\Calculation\Bills;

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

    #[On('mediator-mount-observations-modal')]
    public function ItemDataObstoRespComp($dict){
        $dict['zIndexModal'] = $this->convertStackCountPlusOne();

        $this->dispatch('mount-observations-modal', $dict);
    }

    //
    #[On('mediator-obsto-index-bills-comp-costitems')]
    public function ObsToIndexbillsComp($value){
        $this->dispatch('obsto-index-bills-comp-costitems', $value);
    }

    public function placeholder(){
        return view('livewire.placeholder.index-menu-placeholder');
    }

    public function render()
    {
        return view('livewire.calculation.bills.mediator-bills-component');
    }
}
