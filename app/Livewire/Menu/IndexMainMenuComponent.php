<?php

namespace App\Livewire\Menu;

use Livewire\Component;
use Livewire\Attributes\Isolate;

use App\Livewire\Traits\AdapterLivewireExceptionTrait;

#[Isolate]
class IndexMainMenuComponent extends Component
{
    use AdapterLivewireExceptionTrait;


    public function render()
    {
        return view('livewire.menu.index-main-menu-component');
    }
}
