<?php

namespace App\Livewire\Configuration\RolesPermission\Permission;

use Livewire\Component;
use Livewire\Attributes\On;

use Livewire\Attributes\Lazy;
use App\Livewire\Traits\ModalEnableTrait;
use App\Livewire\Traits\EscapeEnableTrait;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;

#[Lazy]
class MediatorPermissionComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        ModalEnableTrait,
        EscapeEnableTrait;

    public $isProcessingEscape;

    #[On('mediator-mount-open-create-permission-modal')]
    public function MediatorOpenCreatePermissionModal($dict){
        $dict['zIndexModal'] = $this->convertStackCountPlusOne();
        $this->dispatch('mount-open-edit-register-permission-modal', $dict);
    }

    #[On('mediator-mount-open-edit-permission-modal')]
    public function MediatorOpenEditPermissionModal($dict){
        $dict['zIndexModal'] = $this->convertStackCountPlusOne();
        $this->dispatch('mount-open-edit-register-permission-modal', $dict);
    }

    public function placeholder(){
        return view('livewire.placeholder.index-menu-placeholder');
    }
    public function render()
    {
        return view('livewire.configuration.roles-permission.permission.mediator-permission-component');
    }
}
