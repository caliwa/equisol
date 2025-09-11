<?php

namespace App\Livewire\Configuration;

use Livewire\Component;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\On;

use App\Livewire\Traits\AdapterLivewireExceptionTrait;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;
use App\Livewire\Traits\CloseModalClickTrait;

#[Isolate]
class IndexConfigurationComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        AdapterValidateLivewireInputTrait,
        CloseModalClickTrait;

    public $isVisibleIndexConfigurationComponent = false;

    #[On('isVisibleIndexConfigurationComponent')]
    public function setModalVariable($value)
    {
        $this->ResetModalVariables();
        $this->isVisibleIndexConfigurationComponent = $value;
    }

    #[On('mount-modal')]
    public function mount_artificially($dict){
        $this->zIndexModal = $dict['zIndexModal'];

        $this->isVisibleIndexConfigurationComponent = true;
        
        $this->dispatch('escape-enabled');
    }

    public function ResetModalVariables(){
        $this->resetErrorBag();
        $this->reset(array_keys($this->all()));
    }

    public function render()
    {
        return view('livewire.configuration.index-configuration-component');
    }
}
