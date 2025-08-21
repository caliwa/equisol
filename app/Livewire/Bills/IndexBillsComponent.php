<?php

namespace App\Livewire\Bills;

use Livewire\Component;
use App\Models\CostItem;
use App\Models\Currency;
use App\Models\ServiceType;
use Livewire\Attributes\Isolate;
use Illuminate\Support\Collection;
use App\Livewire\Traits\AdapterValidateLivewireInputTrait;

#[Isolate]
class IndexBillsComponent extends Component
{
    use AdapterValidateLivewireInputTrait;

    public int $serviceTypeId;
    public string $serviceTypeName = 'Costos de Importación';
    
    // Usaremos una colección para manejar los datos de forma reactiva
    public Collection $costItems;
    public Collection $currencies;
    
    // Propiedades para un nuevo item
    public string $newStage = 'Destino';
    public string $newConcept = '';

    public function mount()
    {
        // Asegurarse de que el ServiceType exista
        $serviceType = ServiceType::firstOrCreate(['name' => $this->serviceTypeName]);
        $this->serviceTypeId = $serviceType->id;

        $this->currencies = Currency::all();
        $this->loadCostItems();
    }

    public function loadCostItems()
    {
        // Cargar los items ordenados por etapa (Origen primero, luego Destino)
        $this->costItems = CostItem::where('service_type_id', $this->serviceTypeId)
            ->orderBy('stage')
            ->orderBy('concept')
            ->get();
    }

    // Se dispara cada vez que un campo se actualiza en el frontend
    public function updatedCostItems($value, $key)
    {
        // Extrae el ID del item y el campo que se está actualizando
        $parts = explode('.', $key);
        $itemId = $this->costItems[$parts[0]]['id'];
        $field = $parts[1];

        $item = CostItem::find($itemId);
        if ($item) {
            // Asigna el nuevo valor y lo guarda
            $item->{$field} = $value === '' ? null : $value;
            $item->save();
        }

        // Opcional: mostrar una notificación de éxito
        // Flux::toast('Guardado automáticamente.');
    }

    public function addNewItem()
    {
        $this->validate([
            'newConcept' => 'required|string|min:3',
            'newStage' => 'required|in:Origen,Destino',
        ]);
        
        // Crea el nuevo item
        CostItem::create([
            'service_type_id' => $this->serviceTypeId,
            'stage' => $this->newStage,
            'concept' => $this->newConcept,
            'calculation_type' => 'FIXED', // Valor por defecto
        ]);

        // Resetea los campos del formulario y recarga la lista
        $this->reset(['newConcept', 'newStage']);
        $this->loadCostItems();
    }
    
    public function removeItem($itemId)
    {
        CostItem::destroy($itemId);
        $this->loadCostItems(); // Recarga la lista para que desaparezca de la UI
    }


    public function render()
    {
        $groupedItems = $this->costItems->groupBy('stage');
        return view('livewire.bills.index-bills-component', [
            'groupedItems' => $groupedItems,
        ]);
    }
}
