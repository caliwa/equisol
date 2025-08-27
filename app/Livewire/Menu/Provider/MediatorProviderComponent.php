<?php

namespace App\Livewire\Menu\Provider;

use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\RateProvider;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Isolate;
use App\Livewire\Traits\ModalEnableTrait;
use App\Livewire\Traits\EscapeEnableTrait;
use App\Livewire\Traits\ProcessingEscapeTrait;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;

#[Isolate]
class MediatorProviderComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        ModalEnableTrait,
        // EscapeEnableTrait,
        ProcessingEscapeTrait;

    public ?RateProvider $provider = null;

    public $modalConfirmValidationMessage;

    public function mount($provider = null): void
    {
        $this->provider = $provider;
    }

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

    public function placeholder(){
        return view('livewire.placeholder.index-menu-placeholder');
    }


    public function render()
    {
        return view('livewire.menu.provider.mediator-provider-component');
    }
}
