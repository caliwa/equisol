<?php

namespace App\Livewire\Traits;

use Livewire\Attributes\On;

trait EscapeEnableTrait
{
    public $escapeEnabled = true;

    #[On('escape-enabled')]
    public function EscapeEnabled(){
        $this->escapeEnabled = true;
    }

    #[On('escape-disabled')]
    public function EscapeDisabled(){
        $this->escapeEnabled = false;
    }
}
