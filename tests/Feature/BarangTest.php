<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BarangTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat user untuk testing
        $this->user = User::create([
            'username' => 'testuser',
            'password' => bcrypt('password'),
            'nama_lengkap' => 'Test User',
            'telepone' => '081234567890',
            'alamat' => 'Jl. Test No. 123',
            'is_super_admin' => false,
        ]);

        // Buat kategori untuk testing
        $this->category = Category::create([
            'nama_kategori' => 'Pakaian Pria',
            'deskripsi' => 'Kategori untuk pakaian pria',
        ]);
    }

    /** @test */
    public function can_create_barang()
    {
        $barang = Barang::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'nama_barang' => 'Kemeja Batik',
            'ukuran' => 'L',
            'stok' => 10,
        ]);

        $this->assertDatabaseHas('barangs', [
            'nama_barang' => 'Kemeja Batik',
            'ukuran' => 'L',
            'stok' => 10,
        ]);

        $this->assertEquals('Kemeja Batik', $barang->nama_barang);
        $this->assertEquals('L', $barang->ukuran);
        $this->assertEquals(10, $barang->stok);
    }

    /** @test */
    public function cannot_create_barang_without_ukuran()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Barang::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'nama_barang' => 'Kaos Polos',
            'stok' => 5,
        ]);

        $this->assertDatabaseMissing('barangs', [
            'nama_barang' => 'Kaos Polos',
            'stok' => 5
        ]);
    }

    /** @test */
    public function barang_has_default_stok_zero()
    {
        $barang = Barang::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'nama_barang' => 'Celana Jeans',
            'ukuran' => '32',
        ]);

        $this->assertEquals(0, $barang->stok);
    }

    /** @test */
    public function can_update_barang()
    {
        $barang = Barang::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'nama_barang' => 'Jaket Kulit',
            'ukuran' => 'M',
            'stok' => 3,
        ]);

        $barang->update([
            'nama_barang' => 'Jaket Denim',
            'ukuran' => 'L',
            'stok' => 7,
        ]);

        $this->assertDatabaseHas('barangs', [
            'id' => $barang->id,
            'nama_barang' => 'Jaket Denim',
            'ukuran' => 'L',
            'stok' => 7,
        ]);

        $this->assertDatabaseMissing('barangs',[
            'nama_barang' => 'Jaket Kulit',
            'ukuran' => 'M',
            'stok' => 3,
        ]);
    }

    /** @test */
    public function can_soft_delete_barang()
    {
        $barang = Barang::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'nama_barang' => 'Sweater Rajut',
            'ukuran' => 'XL',
            'stok' => 2,
        ]);

        $barang->delete();

        $this->assertSoftDeleted('barangs', [
            'id' => $barang->id,
        ]);

        // Barang tidak muncul di query biasa
        $this->assertNull(Barang::find($barang->id));

        // Tapi masih ada dengan withTrashed
        $this->assertNotNull(Barang::withTrashed()->find($barang->id));
    }

    /** @test */
    public function can_restore_soft_deleted_barang()
    {
        $barang = Barang::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'nama_barang' => 'Hoodie Premium',
            'ukuran' => 'M',
            'stok' => 1,
        ]);

        $barang->delete();
        $barang->restore();

        $this->assertDatabaseHas('barangs', [
            'id' => $barang->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function barang_belongs_to_category()
    {
        $barang = Barang::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'nama_barang' => 'Polo Shirt',
            'ukuran' => 'L',
            'stok' => 7,
        ]);

        $this->assertInstanceOf(Category::class, $barang->category->id);
        $this->assertEquals($this->category->id, $barang->category->id);
        $this->assertEquals('Pakaian Pria', $barang->category->nama_kategori);
    }

    /** @test */
    public function barang_belongs_to_user()
    {
        $barang = Barang::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'nama_barang' => 'Kemeja Flanel',
            'ukuran' => 'XL',
            'stok' => 4,
        ]);

        $this->assertInstanceOf(User::class, $barang->creator);
        $this->assertEquals($this->user->id, $barang->creator->id);
        $this->assertEquals('testuser', $barang->creator->username);
    }

    /** @test */
    public function deleting_category_deletes_barang()
    {
        $barang = Barang::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'nama_barang' => 'Kaos V-Neck',
            'ukuran' => 'M',
            'stok' => 6,
        ]);

        $this->category->delete();

        $this->assertDatabaseMissing('barangs', [
            'id' => $barang->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function deleting_user_deletes_barang()
    {
        $barang = Barang::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'nama_barang' => 'Celana Chino',
            'ukuran' => '30',
            'stok' => 8,
        ]);

        $this->user->delete();

        $this->assertDatabaseMissing('barangs', [
            'id' => $barang->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function nama_barang_max_length_200()
    {
        $longName = str_repeat('a', 200);

        $barang = Barang::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'nama_barang' => $longName,
            'ukuran' => 'L',
            'stok' => 1,
        ]);

        $this->assertEquals(200, strlen($barang->nama_barang));
    }

    /** @test */
    public function ukuran_max_length_100()
    {
        $longSize = str_repeat('b', 100);

        $barang = Barang::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'nama_barang' => 'Baju Koko',
            'ukuran' => $longSize,
            'stok' => 1,
        ]);

        $this->assertEquals(100, strlen($barang->ukuran));
    }

    /** @test */
    public function can_create_barang_with_various_sizes()
    {
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];

        foreach ($sizes as $size) {
            $barang = Barang::create([
                'category_id' => $this->category->id,
                'created_by' => $this->user->id,
                'nama_barang' => "T-Shirt Size {$size}",
                'ukuran' => $size,
                'stok' => 10,
            ]);

            $this->assertDatabaseHas('barangs', [
                'nama_barang' => "T-Shirt Size {$size}",
                'ukuran' => $size,
            ]);
        }
    }

    /** @test */
    public function can_update_stok()
    {
        $barang = Barang::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'nama_barang' => 'Kemeja Oxford',
            'ukuran' => 'L',
            'stok' => 10,
        ]);

        // Tambah stok
        $barang->increment('stok', 5);
        $this->assertEquals(15, $barang->fresh()->stok);

        // Kurangi stok
        $barang->decrement('stok', 3);
        $this->assertEquals(12, $barang->fresh()->stok);
    }

    /** @test */
    public function has_timestamps()
    {
        $barang = Barang::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'nama_barang' => 'Jogger Pants',
            'ukuran' => '32',
            'stok' => 20,
        ]);

        $this->assertNotNull($barang->created_at);
        $this->assertNotNull($barang->updated_at);
    }

    /** @test */
    public function can_create_barang_with_numeric_size()
    {
        $barang = Barang::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'nama_barang' => 'Celana Formal',
            'ukuran' => '34',
            'stok' => 5,
        ]);

        $this->assertDatabaseHas('barangs', [
            'nama_barang' => 'Celana Formal',
            'ukuran' => '34',
        ]);
    }
}
