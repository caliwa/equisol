<?php

namespace App\Livewire\Calculation\Transit;

use Flux\Flux;
use App\Models\Origin;
use Livewire\Component;
use App\Models\TransitMode;
use Livewire\Attributes\On;
use Livewire\Attributes\Isolate;
use Illuminate\Support\Facades\DB;
use App\Livewire\Traits\CloseModalClickTrait;
use App\Livewire\Traits\AdapterLivewireExceptionTrait;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;

#[Isolate]
class TransitTimeManagerComponent extends Component
{
    use AdapterLivewireExceptionTrait,
        AdapterValidateLivewireInputTrait,
        CloseModalClickTrait;

    public $origins;
    public $transitModes;
    public $transitData = [];
    public $initialTransitData = [];

    public $isVisibleIndexTransitTimeManagerComponent = false;

    #[On('isVisibleIndexTransitTimeManagerComponent')]
    public function setModalVariable($value)
    {
        $this->ResetModalVariables();
        $this->isVisibleIndexTransitTimeManagerComponent = $value;
    }

    #[On('mount-open-transit-mode-modal')]
    public function mount_artificially($dict){
        $this->zIndexModal = $dict['zIndexModal'];
        $this->loadData();
        $this->isVisibleIndexTransitTimeManagerComponent = true;
        $this->dispatch('escape-enabled');
    }

    public function loadData()
    {
        $this->transitModes = TransitMode::all();
        $this->origins = Origin::with('transitModes')->orderBy('name')->get();

        foreach ($this->origins as $origin) {
            foreach ($this->transitModes as $mode) {
                $transitRelation = $origin->transitModes->firstWhere('id', $mode->id);
                $days = $transitRelation ? $transitRelation->pivot->days : null;
                $this->transitData[$origin->id][$mode->id] = $days;
            }
        }
        
        $this->initialTransitData = $this->transitData;
    }

// en TransitTimeManagerComponent.php

    public function saveTransitTimes()
    {
        if ($this->transitData == $this->initialTransitData) {
            Flux::toast('No se han realizado cambios.', 'Información');
            $this->dispatch('escape-enabled');
            return;
        }

        try {
            $this->validate([
                'transitData.*.*' => 'nullable|numeric|min:0|integer|max:999999',
            ], [
                'transitData.*.*.numeric' => 'El valor de los días debe ser un número.',
                'transitData.*.*.min' => 'El valor de los días no puede ser negativo.',
                'transitData.*.*.integer' => 'El valor de los días debe ser un número entero.',
                'transitData.*.*.max' => 'El valor de los días no puede ser mayor a 999999.',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            foreach ($e->validator->errors()->getMessages() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }

            Flux::toast($e->getMessage(), 'Error');

            $this->dispatch('escape-enabled');
            return;
        }

        DB::beginTransaction();
        foreach ($this->origins as $origin) {
            $dataToSync = [];
            if (isset($this->transitData[$origin->id])) {
                foreach ($this->transitData[$origin->id] as $modeId => $days) {
                    if (!is_null($days) && $days !== '') {
                        $dataToSync[$modeId] = ['days' => $days];
                    }
                }
            }
            
            $origin->transitModes()->sync($dataToSync);
        }
        DB::commit();
        
        $this->initialTransitData = $this->transitData;
        $this->dispatch('escape-enabled');
        Flux::toast('¡Tiempos de tránsito actualizados con éxito!', 'Éxito');
    }

    public function ResetModalVariables(){
        $this->resetErrorBag();
        $this->reset(array_keys($this->all()));
    }

    public function render()
    {
        return view('livewire.calculation.transit.transit-time-manager-component');
    }
}