<?php

namespace App\Livewire\Calculation\Management\Provider;

use Livewire\Component;
use Livewire\Attributes\Isolate;
use App\Models\RateProvider;

#[Isolate]
class IndexProviderEditorComponent extends Component
{
    public RateProvider $provider;

    public function mount(RateProvider $provider)
    {
        $this->provider = $provider;
    }

    public function render()
    {
        return view('livewire.calculation.management.provider.index-provider-editor-component');
    }
}
