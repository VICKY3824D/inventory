<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nama_kategori' => 'Hijab',
                'deskripsi' => 'Kategori untuk berbagai jenis hijab seperti pashmina, segi empat, dan khimar.'
            ],
            [
                'nama_kategori' => 'Anak',
                'deskripsi' => 'Kategori produk pakaian untuk anak-anak laki-laki dan perempuan.'
            ],
            [
                'nama_kategori' => 'Celana',
                'deskripsi' => 'Kategori celana panjang, pendek, jeans, dan jogger untuk segala usia.'
            ],
            [
                'nama_kategori' => 'Hoodie',
                'deskripsi' => 'Kategori hoodie pria dan wanita, termasuk zipper hoodie dan pullover.'
            ],
            [
                'nama_kategori' => 'Kemeja',
                'deskripsi' => 'Kategori kemeja formal maupun casual dengan berbagai bahan.'
            ],
            [
                'nama_kategori' => 'Gamis',
                'deskripsi' => 'Kategori gamis wanita dengan model syar’i maupun modern.'
            ],
            [
                'nama_kategori' => 'Dress',
                'deskripsi' => 'Kategori dress untuk casual, formal, maupun semi-formal.'
            ],
            [
                'nama_kategori' => 'Kaos',
                'deskripsi' => 'Kategori kaos polos maupun sablon, lengan pendek dan panjang.'
            ],
            [
                'nama_kategori' => 'Rok',
                'deskripsi' => 'Kategori rok plisket, span, flare, dan berbagai model lainnya.'
            ],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        $this->command->info('Seeder kategori berhasil!');
    }
}
