<?php

namespace App\Livewire\Traits;

use Livewire\Attributes\On;

trait EscapeEnableTrait
{
    public $escapeEnabled = true;

    #[On('EscapeEnabled')]
    public function EscapeEnabled(){
        $this->escapeEnabled = true;
    }

    #[On('EscapeDisabled')]
    public function EscapeDisabled(){
        $this->escapeEnabled = false;
    }
}
