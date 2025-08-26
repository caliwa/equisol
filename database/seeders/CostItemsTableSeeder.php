<?php

namespace Database\Seeders;

use App\Models\CostItem;
use App\Models\CostServiceType;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class CostItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Currency::firstOrCreate(['code' => 'COP', 'name' => 'Peso Colombiano']);
        // Primero, obtenemos los IDs que necesitaremos
$maritimoId = CostServiceType::where('name', 'Gastos Mar')->firstOrFail()->id;
        $aereoId = CostServiceType::where('name', 'Gastos Aéreo')->firstOrFail()->id;
        $courierId = CostServiceType::where('name', 'Gastos Courier')->firstOrFail()->id;

        $usdId = Currency::where('code', 'USD')->firstOrFail()->id;
        $copId = Currency::where('code', 'COP')->firstOrFail()->id;

        // Limpiamos la tabla para evitar duplicados
        CostItem::truncate();

        //======================================================================
        // GASTOS MARÍTIMO (GASTOS MAR)
        //======================================================================
        $gastosMar = [
            // --- Origen ---
            ['stage' => 'Origen', 'concept' => 'Traslado', 'currency_id' => $usdId, 'formula' => json_encode(['type' => 'formula', 'expression' => 15])],
            ['stage' => 'Origen', 'concept' => 'Aduana Origen', 'currency_id' => $usdId, 'formula' => json_encode(['type' => 'formula', 'expression' => 78])],
            ['stage' => 'Origen', 'concept' => 'Manejo (BL-doc-varios)', 'currency_id' => $usdId, 'formula' => json_encode(['type' => 'formula', 'expression' => 100])],
            // --- Destino ---
            ['stage' => 'Destino', 'concept' => 'Comisión SIA (agencia de aduana)', 'currency_id' => $copId, 'formula' => json_encode(['type' => 'formula', 'expression' => 'max(CIF * 0.0025, 348900)'])],
            ['stage' => 'Destino', 'concept' => 'Manejo en Puerto', 'currency_id' => $copId, 'formula' => json_encode([
                'type' => 'rules', 'expression' => [
                    'default_value' => 2500000, 'rules' => [ // Asumimos que >20000 es 2.5M
                        ['result' => 900000, 'conditions' => [['variable' => 'PESO', 'operator' => '>=', 'value' => 0], ['variable' => 'PESO', 'operator' => '<=', 'value' => 5000]]],
                        ['result' => 1300000, 'conditions' => [['variable' => 'PESO', 'operator' => '>', 'value' => 5000], ['variable' => 'PESO', 'operator' => '<=', 'value' => 10000]]],
                        ['result' => 1800000, 'conditions' => [['variable' => 'PESO', 'operator' => '>', 'value' => 10000], ['variable' => 'PESO', 'operator' => '<=', 'value' => 15000]]],
                        ['result' => 2500000, 'conditions' => [['variable' => 'PESO', 'operator' => '>', 'value' => 15000], ['variable' => 'PESO', 'operator' => '<=', 'value' => 20000]]],
                    ]
                ]
            ])],
            ['stage' => 'Destino', 'concept' => 'Elaboración Declaraciones', 'currency_id' => $copId, 'formula' => json_encode(['type' => 'formula', 'expression' => 219700])],
            ['stage' => 'Destino', 'concept' => 'Elaboración DAV', 'currency_id' => $copId, 'formula' => json_encode(['type' => 'formula', 'expression' => 236600])],
            ['stage' => 'Destino', 'concept' => 'Inspección', 'currency_id' => $copId, 'formula' => json_encode(['type' => 'formula', 'expression' => 167600])],
            ['stage' => 'Destino', 'concept' => 'Siglo XXI y documental', 'currency_id' => $copId, 'formula' => json_encode(['type' => 'formula', 'expression' => 69900])],
            ['stage' => 'Destino', 'concept' => 'Seguro', 'currency_id' => $copId, 'formula' => json_encode(['type' => 'formula', 'expression' => 'CIF * 0.0010'])],
            ['stage' => 'Destino', 'concept' => 'Gastos operativos y otros', 'currency_id' => $copId, 'formula' => json_encode(['type' => 'formula', 'expression' => 95480])],
            ['stage' => 'Destino', 'concept' => 'Transporte Nacional', 'currency_id' => $copId, 'formula' => json_encode([
                'type' => 'rules', 'expression' => [
                    'default_value' => 6000000, 'rules' => [ // Asumimos que >8000 es 6M
                        ['result' => 700000, 'conditions' => [['variable' => 'PESO', 'operator' => '>=', 'value' => 1], ['variable' => 'PESO', 'operator' => '<=', 'value' => 500]]],
                        ['result' => 1000000, 'conditions' => [['variable' => 'PESO', 'operator' => '>', 'value' => 500], ['variable' => 'PESO', 'operator' => '<=', 'value' => 1000]]],
                        ['result' => 1800000, 'conditions' => [['variable' => 'PESO', 'operator' => '>', 'value' => 1000], ['variable' => 'PESO', 'operator' => '<=', 'value' => 4000]]],
                        ['result' => 2200000, 'conditions' => [['variable' => 'PESO', 'operator' => '>', 'value' => 4000], ['variable' => 'PESO', 'operator' => '<=', 'value' => 8000]]],
                    ]
                ]
            ])],
            ['stage' => 'Destino', 'concept' => 'Arancel', 'currency_id' => null, 'formula' => json_encode(['type' => 'formula', 'expression' => 'CIF * ARANCEL_MANUAL'])],
        ];

        foreach ($gastosMar as $item) {
            $item['service_type_id'] = $maritimoId;
            CostItem::create($item);
        }

        //======================================================================
        // GASTOS AÉREO
        //======================================================================
        $gastosAereo = [
             // --- Origen ---
             ['stage' => 'Origen', 'concept' => 'Traslado', 'currency_id' => $usdId, 'formula' => json_encode(['type' => 'formula', 'expression' => 15])],
             ['stage' => 'Origen', 'concept' => 'Aduana Origen', 'currency_id' => $usdId, 'formula' => json_encode(['type' => 'formula', 'expression' => 30])],
             ['stage' => 'Origen', 'concept' => 'Manejo (BL-doc-varios)', 'currency_id' => $usdId, 'formula' => json_encode(['type' => 'formula', 'expression' => 100])],
             // --- Destino ---
             ['stage' => 'Destino', 'concept' => 'Comisión SIA (agencia de aduana)', 'currency_id' => $copId, 'formula' => json_encode(['type' => 'formula', 'expression' => 'max(CIF * 0.0025, 348900)'])],
             ['stage' => 'Destino', 'concept' => 'Seguro', 'currency_id' => $copId, 'formula' => json_encode(['type' => 'formula', 'expression' => 'CIF * 0.0010'])],
             ['stage' => 'Destino', 'concept' => 'Bodegaje', 'currency_id' => $copId, 'formula' => json_encode([
                 'type' => 'rules', 'expression' => [
                     'default_value' => 3840000, 'rules' => [
                         ['result' => 562300, 'conditions' => [['variable' => 'PESO', 'operator' => '>=', 'value' => 1], ['variable' => 'PESO', 'operator' => '<=', 'value' => 500]]],
                         ['result' => 640000, 'conditions' => [['variable' => 'PESO', 'operator' => '>', 'value' => 500], ['variable' => 'PESO', 'operator' => '<=', 'value' => 1000]]],
                         ['result' => 1280000, 'conditions' => [['variable' => 'PESO', 'operator' => '>', 'value' => 1000], ['variable' => 'PESO', 'operator' => '<=', 'value' => 4000]]],
                         ['result' => 2200000, 'conditions' => [['variable' => 'PESO', 'operator' => '>', 'value' => 4000], ['variable' => 'PESO', 'operator' => '<=', 'value' => 8000]]],
                     ]
                 ]
             ])],
             ['stage' => 'Destino', 'concept' => 'Manejo del Bodegaje', 'currency_id' => $copId, 'formula' => json_encode(['type' => 'formula', 'expression' => 120000])],
        ];

        foreach ($gastosAereo as $item) {
            $item['service_type_id'] = $aereoId;
            CostItem::create($item);
        }

        //======================================================================
        // GASTOS COURIER
        //======================================================================
        $gastosCourier = [
            ['stage' => 'Destino', 'concept' => 'Seguro', 'currency_id' => $usdId, 'formula' => json_encode(['type' => 'formula', 'expression' => 'max(VALOR_MERCANCIA * 0.01, 16.5)'])],
            ['stage' => 'Destino', 'concept' => 'Pick up', 'currency_id' => $usdId, 'formula' => json_encode(['type' => 'formula', 'expression' => 'PESO * 1.10'])],
            ['stage' => 'Destino', 'concept' => 'Firma Doc', 'currency_id' => $usdId, 'formula' => json_encode(['type' => 'formula', 'expression' => 12.5])],
            ['stage' => 'Destino', 'concept' => 'Entrega Destino', 'currency_id' => $usdId, 'formula' => json_encode(['type' => 'formula', 'expression' => 'PESO * 1.10'])],
            ['stage' => 'Destino', 'concept' => 'Manejo DHL', 'currency_id' => $usdId, 'formula' => json_encode([
                'type' => 'rules', 'expression' => [
                    'default_value' => 52.14, 'rules' => [
                        ['result' => 0, 'conditions' => [['variable' => 'CIF', 'operator' => '>=', 'value' => 0], ['variable' => 'CIF', 'operator' => '<=', 'value' => 100]]],
                        ['result' => 28.49, 'conditions' => [['variable' => 'CIF', 'operator' => '>', 'value' => 100], ['variable' => 'CIF', 'operator' => '<=', 'value' => 500]]],
                        ['result' => 52.14, 'conditions' => [['variable' => 'CIF', 'operator' => '>', 'value' => 500]]],
                    ]
                ]
            ])],
            ['stage' => 'Destino', 'concept' => 'Fuel', 'currency_id' => $usdId, 'formula' => json_encode(['type' => 'formula', 'expression' => 132.27])],
            ['stage' => 'Destino', 'concept' => 'Arancel', 'currency_id' => $usdId, 'formula' => json_encode(['type' => 'formula', 'expression' => 'CIF * 0.10'])],
        ];

        foreach ($gastosCourier as $item) {
            $item['service_type_id'] = $courierId;
            CostItem::create($item);
        }
    }
}