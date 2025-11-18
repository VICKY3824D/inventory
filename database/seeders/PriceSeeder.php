<?php

namespace Database\Seeders;

use App\Models\Price;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Price::create([
            'product_id' => 1,
            'harga' => 97500.00,
        ]);

        Price::create([
            'product_id' => 2,
            'harga' => 165000.00,
        ]);

        $this->command->info('Seeder Price berhasil!');
    }
}
