<?php

namespace App\Livewire\Traits;

use App\Models\Rate;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\WeightTier;

trait CalculateMasterRateTrait
{
    /**
     * Calcula el costo de un servicio maestro (Pick Up, Flete) basado en el tipo de servicio, país y peso.
     *
     * @param string $serviceTypeName El nombre del tipo de servicio (ej: "Flete Aéreo").
     * @param string $countryName El nombre del país de origen.
     * @param float $weight El peso para el cálculo.
     * @return float|null El costo calculado o null si no se encuentra una tarifa.
     */
    public function calculateMasterRate(string $serviceTypeName, string $countryName, float $weight): ?float
    {
        // 1. Buscar el servicio específico que coincide con el tipo y el origen.
        $service = Service::whereHas('serviceType', function ($query) use ($serviceTypeName) {
            $query->where('name', $serviceTypeName);
        })->whereHas('origin', function ($query) use ($countryName) {
            $query->where('name', $countryName);
        })->with(['rates.weightTier', 'origin', 'serviceType'])->first();

        // Si no se encuentra una combinación de servicio/país, no se puede calcular.
        if (!$service) {
            return null;
        }

        $calculatedPrice = null;

        // 2. Ordenar las tarifas por su 'display_order' para evaluarlas secuencialmente.
        $sortedRates = $service->rates->sortBy('weightTier.display_order');


        // 3. Iterar sobre las tarifas para encontrar la que coincida con el peso.
        foreach ($sortedRates as $rate) {
            $tierLabel = $rate->weightTier->label; // ej: "<1000", "<=5000"

            // Usamos regex para extraer el operador y el valor del límite.
            if (preg_match('/^(<=|>=|<|>)\s*([\d.]+)/', $tierLabel, $matches)) {
                $operator = $matches[1];
                $limitValue = (float) $matches[2];

                // Comprobamos si el peso cumple la condición de este tramo.
                $conditionMet = match ($operator) {
                    '<' => $weight < $limitValue,
                    '<=' => $weight <= $limitValue,
                    '>' => $weight > $limitValue,
                    '>=' => $weight >= $limitValue,
                    default => false,
                };

                
                // Si la condición se cumple, calculamos el precio y terminamos el bucle.
                if ($conditionMet) {
                    $calculatedPrice = $weight * $rate->rate_value;
                    break; 
                }
            }
        }
        
        // Si después de evaluar todas las reglas no se encontró un precio
        // (podría ser porque el peso es mayor al último tramo), devolvemos null.
        if (is_null($calculatedPrice)) {
            return null;
        }

        // 4. El costo final no puede ser menor que la tarifa mínima del servicio.
        $minimumCharge = $service->minimum_charge;

        return max($calculatedPrice, $minimumCharge);
    }
}