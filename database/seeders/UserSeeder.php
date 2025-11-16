<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data super admin
        User::create([
            'username' => 'superadmin',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Eni',
            'telepone' => '085229297152',
            'alamat' => 'Terbis, RT 02, RW 01, Kismantoro',
            'is_super_admin' => true,
        ]);

        // Data admin biasa
        User::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'nama_lengkap' => 'Lia',
            'telepone' => '081234567891',
            'alamat' => 'Terbis, RT 01, RW 01, Kismantoro',
            'is_super_admin' => false,
        ]);

        $this->command->info('Seeder User berhasil!');
    }
}
