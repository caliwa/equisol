<?php

namespace App\Livewire\Traits;

trait ResetValidationWrapperTrait
{

    public function resetValidationWrapper(){
        $this->resetErrorBag();
    }
}