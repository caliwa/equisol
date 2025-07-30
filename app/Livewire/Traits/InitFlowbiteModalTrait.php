<?php

namespace App\Livewire\Traits;

trait InitFlowbiteModalTrait
{
    public function initFlowbiteModal(){
        $this->js('initFlowbite();');
    }
}