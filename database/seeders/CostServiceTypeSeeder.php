<?php

namespace Database\Seeders;

use App\Models\CostServiceType;
use Illuminate\Database\Seeder;

class CostServiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        CostServiceType::create(['name' => 'Gastos Mar']);
        CostServiceType::create(['name' => 'Gastos Aéreo']);
        CostServiceType::create(['name' => 'Gastos Courier']);
    }
}