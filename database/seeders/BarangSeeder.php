<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Barang::create([
            'category_id' => 1,
            'nama_barang' => 'Hijab',
            'ukuran' => 'XXL',
            'stok' => 17,
            'created_by' => 1,
        ]);

        Barang::create([
            'category_id' => 5,
            'nama_barang' => 'Kemeja',
            'ukuran' => 'L',
            'stok' => 50,
            'created_by' => 2,
        ]);

        $this->command->info('Seeder Barang berhasil!');
    }
}
