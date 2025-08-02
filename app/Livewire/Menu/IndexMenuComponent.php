<?php

namespace App\Livewire\Menu;

use Flux\Flux;
use App\Models\Rate;
use App\Models\Origin;
use App\Models\Service;
use Livewire\Component;
use App\Models\WeightTier;
use App\Models\ServiceType;
use Illuminate\Support\Facades\DB;

class IndexMenuComponent extends Component
{
    public array $table_columns = [];
    public array $rows_data = [];
    
    public string $serviceTypeName = 'Pick Up Aéreo'; 

    public function mount()
    {
        $this->loadRateTableData();
    }

    public function loadRateTableData()
    {
        $serviceType = ServiceType::firstOrCreate(['name' => $this->serviceTypeName]);

        $services = Service::where('service_type_id', $serviceType->id)
            ->with(['origin', 'rates.weightTier'])
            ->whereHas('origin')
            ->join('origins', 'services.origin_id', '=', 'origins.id')
            ->orderBy('origins.name')
            ->select('services.*')
            ->get();

        $weightTiers = WeightTier::orderBy('display_order')->get();

        $this->table_columns = [['id' => 'tier_label', 'name' => 'tier_label', 'label' => 'Tarifa']];
        foreach ($services as $service) {
            $this->table_columns[] = [
                'id' => $service->id,
                'name' => 'service_' . $service->id,
                'label' => $service->origin->name,
                'origin_id' => $service->origin_id
            ];
        }

        $this->rows_data = [];
        $minimaRow = ['tier_label' => 'Mínima', 'tier_id' => null];
        foreach ($services as $service) {
            $minimaRow['service_' . $service->id] = number_format($service->minimum_charge, 2, '.', '');
        }
        $this->rows_data[] = $minimaRow;

        foreach ($weightTiers as $tier) {
            $currentRow = ['tier_label' => $tier->label, 'tier_id' => $tier->id];
            foreach ($services as $service) {
                $rate = $service->rates->firstWhere('weight_tier_id', $tier->id);
                $currentRow['service_' . $service->id] = $rate ? number_format($rate->rate_value, 2, '.', '') : '0.00';
            }
            $this->rows_data[] = $currentRow;
        }
    }

    public function save()
    {
        DB::transaction(function () {
            foreach ($this->table_columns as $column) {
                if (isset($column['origin_id'])) {
                    Origin::where('id', $column['origin_id'])->update(['name' => $column['label']]);
                }
            }
            foreach ($this->rows_data as $row) {
                $tierId = $row['tier_id'];
                $tierLabel = $row['tier_label'];

                if (is_null($tierId) && $tierLabel === 'Mínima') {
                    foreach ($row as $key => $value) {
                        if (str_starts_with($key, 'service_')) {
                            Service::where('id', str_replace('service_', '', $key))->update(['minimum_charge' => $value]);
                        }
                    }
                } elseif (!is_null($tierId)) {
                    WeightTier::where('id', $tierId)->update(['label' => $tierLabel]);
                    foreach ($row as $key => $value) {
                        if (str_starts_with($key, 'service_')) {
                            Rate::updateOrCreate(
                                ['service_id' => str_replace('service_', '', $key), 'weight_tier_id' => $tierId],
                                ['rate_value' => $value]
                            );
                        }
                    }
                }
            }
        });
        Flux::toast('Datos guardados correctamente.');
        $this->loadRateTableData();
    }

    public function addRow($label)
    {
        if (empty($label)) {
            Flux::toast('La etiqueta de la fila no puede estar vacía.', 'error');
            return;
        }
        $lastTier = WeightTier::orderBy('display_order', 'desc')->first();
        WeightTier::create([
            'label' => $label,
            'min_weight' => ($lastTier->max_weight ?? -1) + 1,
            'max_weight' => ($lastTier->max_weight ?? 0) + 1000,
            'display_order' => ($lastTier->display_order ?? 0) + 1,
        ]);
        $this->loadRateTableData();
    }

    public function addColumn($name)
    {
        if (empty($name)) {
            Flux::toast('El nombre del país no puede estar vacío.', 'error');
            return;
        }
        DB::transaction(function () use ($name) {
            $serviceType = ServiceType::firstOrCreate(['name' => $this->serviceTypeName]);
            $origin = Origin::create(['name' => $name]);
            Service::create([
                'origin_id' => $origin->id,
                'service_type_id' => $serviceType->id,
                'minimum_charge' => 0,
            ]);
        });
        $this->loadRateTableData();
    }

    /**
     * Elimina una columna (Servicio, Origen y sus tarifas).
     */
    public function removeColumn($serviceId)
    {
        DB::transaction(function() use ($serviceId) {
            $service = Service::find($serviceId);
            if ($service) {
                $origin = $service->origin;
                // El onDelete('cascade') de la migración se encarga de las tarifas.
                $service->delete();
                // Opcional: borrar el origen si no tiene más servicios.
                if ($origin && !$origin->services()->exists()) {
                    $origin->delete();
                }
            }
        });
        $this->loadRateTableData();
    }

    /**
     * Elimina una fila (WeightTier y sus tarifas).
     */
    public function removeRow($tierId)
    {
        // El onDelete('cascade') se encargará de borrar las tarifas asociadas.
        WeightTier::destroy($tierId);
        $this->loadRateTableData();
    }

    public function render()
    {
        return view('livewire.menu.index-menu-component');
    }
}
