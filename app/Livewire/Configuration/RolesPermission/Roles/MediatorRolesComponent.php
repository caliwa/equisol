<?php

namespace App\Livewire\Configuration\RolesPermission\Roles;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;

use App\Livewire\Traits\AdapterLivewireExceptionTrait;
use App\Livewire\Traits\ModalEnableTrait;
use App\Livewire\Traits\EscapeEnableTrait;

#[Lazy]
class MediatorRolesComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        ModalEnableTrait,
        EscapeEnableTrait;

    public $isProcessingEscape;

    #[On('mediator-mount-open-create-roles-modal')]
    public function MediatorOpenCreateRolesModal($dict){
        $dict['zIndexModal'] = $this->convertStackCountPlusOne();
        $this->dispatch('mount-open-edit-register-roles-modal', $dict);
    }

    #[On('mediator-mount-open-edit-roles-modal')]
    public function MediatortOpenEditRolesModal($dict){
        $dict['zIndexModal'] = $this->convertStackCountPlusOne();
        $this->dispatch('mount-open-edit-register-roles-modal', $dict);
    }

    #[On('mediator-mount-assign-permission-to-roles-modal')]
    public function MediatorAssignPermissionToRolesModal($dict){
        $dict['zIndexModal'] = $this->convertStackCountPlusOne();
        $this->dispatch('mount-assign-permission-to-roles-modal', $dict);
    }

    public function placeholder(){
        return view('livewire.placeholder.index-menu-placeholder');
    }

    public function render()
    {
        return view('livewire.configuration.roles-permission.roles.mediator-roles-component');
    }
}
