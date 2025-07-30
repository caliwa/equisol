<?php

namespace App\Livewire\Traits;

use Livewire\Attributes\On;

trait ModalEnableTrait
{
    public $modalStackCount = 0;
    public $modalStackCountExternal = 0;

    private function convertStackCountPlusOne(){
        return $this->modalStackCountExternal += 9;
    }

    public function MediatorInitialized(){
        $this->dispatch('unblock-sidebar');
    }

    #[On('MediatorSetModalTrue')]
    public function SetModalTrue($modal_to_invisible){
        $this->dispatch($modal_to_invisible, true);
    }

    #[On('MediatorSetModalFalse')]
    public function SetModalFalse($modal_to_invisible){
        $this->js("
            const element = document.getElementById('".$modal_to_invisible."');
            if (element && !element.classList.contains('fade-out-scale')) {
                element.classList.add('fade-out-scale');
            }
        ");
        $this->dispatch($modal_to_invisible, false);
    }

    #[On('OnlyMediatorSetModalFalse')]
    public function OnlySetModalFalse($modal_to_invisible){
        if (property_exists($this, $modal_to_invisible)) {
            $this->js("
                const element = document.getElementById('".$modal_to_invisible."');
                if (element && !element.classList.contains('fade-out-scale')) {
                    element.classList.add('fade-out-scale');
                }
            ");
            $this->$modal_to_invisible = false;
        }
    }

    
}