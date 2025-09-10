<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransitModeSeeder extends Seeder
{
    /**
     */
    public function run(): void
    {
        DB::table('transit_modes')->insert([
            ['name' => 'maritime', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'aerial', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'courrier', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
