<?php

namespace App\Livewire\Menu;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;
use App\Livewire\Traits\ModalEnableTrait;
use App\Livewire\Traits\EscapeEnableTrait;
use App\Livewire\Traits\ProcessingEscapeTrait;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;

#[Lazy]
class MediatorMainMenuComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        ModalEnableTrait,
        // EscapeEnableTrait,
        ProcessingEscapeTrait;

    #[On('mediator-mount-transit-model')]
    public function MediatorTransitModel($dict){
        $dict['zIndexModal'] = $this->convertStackCountPlusOne();
        $this->dispatch('mount-open-transit-mode-modal', $dict);
    }

    #[On('mediator-mount-currency-manager')]
    public function MediatorCurrencyManager($dict){
        $dict['zIndexModal'] = $this->convertStackCountPlusOne();
        $this->dispatch('mount-open-currency-manager', $dict);
    }

    public function placeholder(){
        return view('livewire.placeholder.index-menu-placeholder');
    }

    public function render()
    {
        return view('livewire.menu.mediator-main-menu-component');
    }
}
