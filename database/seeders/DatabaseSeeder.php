<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        User::firstOrCreate(
            ['email' => 'admin@example.com'], // puedes cambiar el correo
            [
                'name' => 'admin',
                'password' => Hash::make('admin123'),
            ]
        );
        $this->call(FreightRatesSeeder::class);
        $this->call(CostServiceTypeSeeder::class);
        $this->call(WorldSeeder::class);
        $this->call(CostItemsTableSeeder::class);
        $this->call(DhlPricingSeeder::class);
        $this->call(TransitModeSeeder::class);
    }
}
