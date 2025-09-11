<?php

namespace App\Livewire\Configuration\User\RegisterUser;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;

use App\Livewire\Traits\ModalEnableTrait;
use App\Livewire\Traits\EscapeEnableTrait;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;

#[Lazy]
class MediatorRegisterUserComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        ModalEnableTrait,
        EscapeEnableTrait;

    public $isProcessingEscape;

    #[On('mediator-mount-register-user-modal')]
    public function MediatorRegisterUserModal($dict){
        $dict['zIndexModal'] = $this->convertStackCountPlusOne();

        $this->dispatch('mount-edit-register-user-modal', $dict);
    }

    #[On('mediator-mount-edit-user-modal')]
    public function MediatorEditUserModal($dict){
        $dict['zIndexModal'] = $this->convertStackCountPlusOne();
        $this->dispatch('mount-edit-register-user-modal', $dict);
    }

    public function placeholder(){
        return view('livewire.placeholder.index-menu-placeholder');
    }

    public function render()
    {
        return view('livewire.configuration.user.register-user.mediator-register-user-component');
    }
}
