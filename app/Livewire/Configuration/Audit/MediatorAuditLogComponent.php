<?php

namespace App\Livewire\Configuration\Audit;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;

use App\Livewire\Traits\ModalEnableTrait;
use App\Livewire\Traits\EscapeEnableTrait;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;

#[Lazy]
class MediatorAuditLogComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        ModalEnableTrait,
        EscapeEnableTrait;

    public $isProcessingEscape;

    public function placeholder(){
        return view('livewire.placeholder.index-menu-placeholder');
    }


    public function render()
    {
        return view('livewire.configuration.audit.mediator-audit-log-component');
    }
}
