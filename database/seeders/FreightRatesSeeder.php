<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Origin;
use App\Models\ServiceType;
use App\Models\WeightTier;
use App\Models\Service;
use App\Models\Rate;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;

class FreightRatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Este seeder poblará la base de datos con tarifas para un servicio
     * que SÍ utiliza monedas, como 'Flete Aéreo'.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Definir y crear las monedas que vamos a usar.
            $usd = Currency::firstOrCreate(['code' => 'USD'], ['name' => 'US Dollar']);
            $eur = Currency::firstOrCreate(['code' => 'EUR'], ['name' => 'Euro']);

            // 2. Definir el nuevo tipo de servicio.
            $serviceType = ServiceType::firstOrCreate(['name' => 'Pick Up Aéreo']);

            // 3. Crear niveles de peso asociados al tipo de servicio
            $weightTiersData = [
                ['label' => '<45', 'min_weight' => 0, 'max_weight' => 44, 'display_order' => 1],
                ['label' => '>=45', 'min_weight' => 45, 'max_weight' => 99, 'display_order' => 2],
                ['label' => '>=100', 'min_weight' => 100, 'max_weight' => 249, 'display_order' => 3],
                ['label' => '>=250', 'min_weight' => 250, 'max_weight' => 299, 'display_order' => 4],
                ['label' => '>300', 'min_weight' => 300, 'max_weight' => 499, 'display_order' => 5],
                ['label' => '>=500', 'min_weight' => 500, 'max_weight' => 999, 'display_order' => 6],
                ['label' => '<=1000', 'min_weight' => 1000, 'max_weight' => 99999, 'display_order' => 7],
            ];

            $weightTiers = [];
            foreach ($weightTiersData as $tierData) {
                $weightTiers[] = WeightTier::firstOrCreate(
                    [
                        'label' => $tierData['label'],
                        'service_type_id' => $serviceType->id, // <- Clave para hacerlos únicos por servicio
                    ],
                    array_merge($tierData, ['service_type_id' => $serviceType->id])
                );
            }

            // 4. Matriz de datos que incluye la moneda por origen
            $freightData = [
                'China' => [
                    'currency' => $usd,
                    'minima' => 160.00,
                    'rates' => [3.20, 3.20, 2.80, 2.50, 2.50, 2.50, 2.50]
                ],
                'Italia' => [
                    'currency' => $eur,
                    'minima' => 220.00,
                    'rates' => [2.55, 2.55, 2.55, 2.55, 2.55, 2.55, 2.55]
                ],
                'Alemania' => [
                    'currency' => $eur,
                    'minima' => 187.00,
                    'rates' => [5.24, 5.24, 4.31, 4.31, 2.91, 2.33, 2.33]
                ],
            ];

            // 5. Recorrer los datos para crear servicios y tarifas
            foreach ($freightData as $originName => $data) {
                $origin = Origin::firstOrCreate(['name' => $originName]);

                // Crear el servicio ASIGNANDO LA MONEDA
                $service = Service::firstOrCreate(
                    [
                        'origin_id' => $origin->id,
                        'service_type_id' => $serviceType->id,
                        'currency_id' => $data['currency']->id,
                    ],
                    [
                        'minimum_charge' => $data['minima']
                    ]
                );

                // Crear las tarifas para cada nivel de peso
                foreach ($weightTiers as $tierIndex => $weightTier) {
                    Rate::firstOrCreate(
                        [
                            'service_id' => $service->id,
                            'weight_tier_id' => $weightTier->id,
                        ],
                        [
                            'rate_value' => $data['rates'][$tierIndex]
                        ]
                    );
                }
            }
        });

        $this->command->info('Seeder de tarifas de Flete Aéreo (con moneda) ejecutado exitosamente!');
    }
}
