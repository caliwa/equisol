<?php

namespace App\Livewire\Configuration;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;
use App\Livewire\Traits\ModalEnableTrait;
use App\Livewire\Traits\EscapeEnableTrait;
use App\Livewire\Traits\ProcessingEscapeTrait;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;

#[Lazy]
class MediatorConfigurationComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        ModalEnableTrait,
        EscapeEnableTrait,
        ProcessingEscapeTrait;


    public function render()
    {
        return view('livewire.configuration.mediator-configuration-component');
    }
}
