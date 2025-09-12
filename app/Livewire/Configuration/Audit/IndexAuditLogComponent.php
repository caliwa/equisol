<?php

namespace App\Livewire\Configuration\Audit;

use Livewire\Component;
use Livewire\Attributes\Isolate;

use App\Livewire\Traits\AdapterLivewireExceptionTrait;

#[Isolate]
class IndexAuditLogComponent extends Component
{
    use AdapterLivewireExceptionTrait;

    public function render()
    {
        return view('livewire.configuration.audit.index-audit-log-component');
    }
}
