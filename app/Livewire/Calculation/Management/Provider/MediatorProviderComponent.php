<?php

namespace App\Livewire\Calculation\Management\Provider;

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

    public function mount($provider = null): void
    {
        $this->provider = $provider;
    }

    public function placeholder(){
        return view('livewire.placeholder.index-menu-placeholder');
    }


    public function render()
    {
        return view('livewire.calculation.management.provider.mediator-provider-component');
    }
}
