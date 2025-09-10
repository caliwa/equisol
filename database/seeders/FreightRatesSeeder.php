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
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Definir monedas
            $usd = Currency::firstOrCreate(['code' => 'USD'], ['name' => 'US Dollar']);
            $eur = Currency::firstOrCreate(['code' => 'EUR'], ['name' => 'Euro']);

            // 2. Definir tipos de servicio
            $serviceTypes = [
                'Pick Up Aéreo' => [
                    'weight_tiers' => [
                        ['label' => '<1000', 'min_weight' => 0, 'display_order' => 1],
                        ['label' => '<2000', 'min_weight' => 1000, 'display_order' => 2],
                        ['label' => '<3000', 'min_weight' => 2000, 'display_order' => 3],
                        ['label' => '<4000', 'min_weight' => 3000, 'display_order' => 4],
                        ['label' => '<5000', 'min_weight' => 4000, 'display_order' => 5],
                        ['label' => '<6000', 'min_weight' => 5000, 'display_order' => 6],
                    ],
                    'data' => [
                        'Italia' => ['currency' => $eur, 'minima' => 50.00, 'rates' => [0.17, 0.14, 0.12, 0.12, 0.11, 0.11]],
                        'Dinamarca' => ['currency' => $eur, 'minima' => 130.00, 'rates' => [1.10, 1.10, 0.72, 0.71, 0.50, 0.40]],
                        'Suecia' => ['currency' => $eur, 'minima' => 125.00, 'rates' => [0.25, 0.25, 0.25, 0.25, 0.25, 0.25]],
                        'Reino Unido' => ['currency' => $eur, 'minima' => 150.00, 'rates' => [0.17, 0.13, 0.12, 0.12, 0.11, 0.11]],
                        'Países Bajos' => ['currency' => $eur, 'minima' => 300.00, 'rates' => [0.40, 0.20, 0.16, 0.15, 0.14, 0.13]],
                        'Alemania' => ['currency' => $eur, 'minima' => 350.00, 'rates' => [0.45, 0.22, 0.18, 0.16, 0.15, 0.14]],
                        'Estados Unidos' => ['currency' => $usd, 'minima' => 130.00, 'rates' => [2.24, 2.24, 2.24, 2.24, 2.24, 2.24]],
                        'Brasil' => ['currency' => $usd, 'minima' => 50.00, 'rates' => [0.38, 0.38, 0.38, 0.38, 0.38, 0.38]],
                        'España' => ['currency' => $eur, 'minima' => 300.00, 'rates' => [1.01, 1.01, 1.01, 1.01, 1.01, 1.01]],
                        'China' => ['currency' => $usd, 'minima' => 110.00, 'rates' => [0.76, 0.76, 0.76, 0.76, 0.76, 0.76]],
                    ]
                ],
                'Pick Up Marítimo' => [
                    'weight_tiers' => [
                        ['label' => '<45', 'min_weight' => 0, 'display_order' => 1],
                        ['label' => '>=45', 'min_weight' => 45, 'display_order' => 2],
                        ['label' => '>=100', 'min_weight' => 100, 'display_order' => 3],
                        ['label' => '>=250', 'min_weight' => 250, 'display_order' => 4],
                        ['label' => '>=300', 'min_weight' => 300, 'display_order' => 5],
                        ['label' => '>=500', 'min_weight' => 500, 'display_order' => 6],
                    ],
                    'data' => [
                        'Italia' => ['currency' => $eur, 'minima' => 80.00, 'rates' => [0.20, 0.19, 0.18, 0.15, 0.14, 0.14]],
                        'Dinamarca' => ['currency' => $eur, 'minima' => 300.00, 'rates' => [0.51, 0.54, 0.62, 0.70, 0.77, 0.80]],
                        'Suecia' => ['currency' => $eur, 'minima' => 400.00, 'rates' => [0.58, 0.58, 0.66, 0.70, 0.77, 0.80]],
                        'Reino Unido' => ['currency' => $eur, 'minima' => 150.00, 'rates' => [0.17, 0.13, 0.12, 0.12, 0.11, 0.11]],
                        'Países Bajos' => ['currency' => $eur, 'minima' => 140.00, 'rates' => [0.18, 0.17, 0.13, 0.12, 0.12, 0.11]],
                        'Alemania' => ['currency' => $eur, 'minima' => 350.00, 'rates' => [0.45, 0.22, 0.18, 0.16, 0.15, 0.14]],
                        'Estados Unidos' => ['currency' => $usd, 'minima' => 125.00, 'rates' => [2.56, 2.16, 1.64, 1.52, 1.45, 1.30]],
                        'Brasil' => ['currency' => $usd, 'minima' => 365.00, 'rates' => [0.36, 0.36, 0.36, 0.36, 0.36, 0.36]],
                        'España' => ['currency' => $eur, 'minima' => 450.00, 'rates' => [0.51, 0.36, 0.25, 0.20, 0.17, 0.15]],
                        'China' => ['currency' => $usd, 'minima' => 115.00, 'rates' => [0.12, 0.08, 0.08, 0.07, 0.07, 0.05]],
                    ]
                ],
                'Flete Aéreo' => [
                    'weight_tiers' => [
                        ['label' => '<45', 'min_weight' => 0, 'display_order' => 1],
                        ['label' => '>=45', 'min_weight' => 45, 'display_order' => 2],
                        ['label' => '>=100', 'min_weight' => 100, 'display_order' => 3],
                        ['label' => '>=250', 'min_weight' => 250, 'display_order' => 4],
                        ['label' => '>=300', 'min_weight' => 300, 'display_order' => 5],
                        ['label' => '>=500', 'min_weight' => 500, 'display_order' => 6],
                        ['label' => '>=1000', 'min_weight' => 1000, 'display_order' => 7],
                    ],
                    'data' => [
                        'Italia' => ['currency' => $eur, 'minima' => 220.00, 'rates' => [2.55, 2.55, 2.60, 2.60, 2.50, 2.40, 2.40]],
                        'Dinamarca' => ['currency' => $eur, 'minima' => 101.00, 'rates' => [2.55, 2.55, 2.55, 2.55, 2.55, 2.55, 2.55]],
                        'Suecia' => ['currency' => $eur, 'minima' => 84.00, 'rates' => [3.97, 3.97, 3.97, 3.97, 3.97, 3.97, 3.97]],
                        'Reino Unido' => ['currency' => $eur, 'minima' => 187.00, 'rates' => [5.24, 5.24, 4.31, 4.31, 2.91, 2.33, 2.33]],
                        'Países Bajos' => ['currency' => $eur, 'minima' => 103.00, 'rates' => [2.65, 2.65, 2.65, 2.65, 2.65, 2.65, 2.65]],
                        'Alemania' => ['currency' => $eur, 'minima' => 400.00, 'rates' => [3.20, 3.20, 2.80, 2.50, 2.50, 2.50, 2.50]],
                        'Estados Unidos' => ['currency' => $usd, 'minima' => 160.00, 'rates' => [0.70, 0.70, 0.70, 0.70, 0.70, 0.70, 0.70]],
                        'Brasil' => ['currency' => $usd, 'minima' => 172.00, 'rates' => [2.00, 1.90, 1.70, 1.70, 1.70, 1.70, 1.70]],
                        'España' => ['currency' => $eur, 'minima' => 160.00, 'rates' => [3.00, 3.00, 3.00, 1.85, 1.85, 1.75, 1.65]],
                        'China' => ['currency' => $usd, 'minima' => 116.00, 'rates' => [6.87, 6.87, 6.87, 6.87, 6.87, 6.87, 6.87]],
                    ]
                ],
                'Flete Marítimo' => [
                    'weight_tiers' => [
                        ['label' => '>=0', 'min_weight' => 0, 'display_order' => 1],
                    ],
                    'data' => [
                        'Italia' => ['currency' => $eur, 'minima' => 60.00, 'rates' => [0.04]],
                        'Dinamarca' => ['currency' => $eur, 'minima' => 70.00, 'rates' => [0.068]],
                        'Suecia' => ['currency' => $eur, 'minima' => 75.00, 'rates' => [0.068]],
                        'Reino Unido' => ['currency' => $eur, 'minima' => 140.00, 'rates' => [0.113]],
                        'Países Bajos' => ['currency' => $eur, 'minima' => 82.00, 'rates' => [0.06]],
                        'Alemania' => ['currency' => $eur, 'minima' => 45.00, 'rates' => [0.04]],
                        'Estados Unidos' => ['currency' => $usd, 'minima' => 100.00, 'rates' => [0.06]],
                        'Brasil' => ['currency' => $usd, 'minima' => 75.00, 'rates' => [0.07]],
                        'España' => ['currency' => $eur, 'minima' => 130.00, 'rates' => [0.11]],
                        'China' => ['currency' => $usd, 'minima' => 40.00, 'rates' => [0.038]],
                    ]
                ]
            ];

            // 3. Procesar cada tipo de servicio
            foreach ($serviceTypes as $serviceTypeName => $serviceTypeData) {
                $serviceType = ServiceType::firstOrCreate(['name' => $serviceTypeName]);

                // Crear niveles de peso
                $weightTiers = [];
                foreach ($serviceTypeData['weight_tiers'] as $tierData) {
                    $weightTiers[] = WeightTier::firstOrCreate(
                        ['label' => $tierData['label'], 'service_type_id' => $serviceType->id],
                        array_merge($tierData, ['service_type_id' => $serviceType->id])
                    );
                }

                // Crear servicios y tarifas
                foreach ($serviceTypeData['data'] as $originName => $data) {
                    $origin = Origin::firstOrCreate(['name' => $originName]);

                    $service = Service::firstOrCreate(
                        ['origin_id' => $origin->id, 'service_type_id' => $serviceType->id],
                        [
                            'minimum_charge' => $data['minima'],
                            'currency_id' => $data['currency']->id ?? null
                        ]
                    );

                    foreach ($weightTiers as $tierIndex => $weightTier) {
                        // Verificar que exista la tarifa para este nivel
                        if (isset($data['rates'][$tierIndex])) {
                            Rate::firstOrCreate(
                                ['service_id' => $service->id, 'weight_tier_id' => $weightTier->id],
                                ['rate_value' => $data['rates'][$tierIndex]]
                            );
                        }
                    }
                }
            }
        });

        $this->command->info('Seeder completo de tarifas de transporte ejecutado exitosamente!');
    }
}