<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Category $category;
    protected Barang $barang1;
    protected Barang $barang2;

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

        // Buat beberapa barang untuk testing
        $this->barang1 = Barang::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'nama_barang' => 'Kemeja Batik',
            'ukuran' => 'L',
            'stok' => 50,
        ]);

        $this->barang2 = Barang::create([
            'category_id' => $this->category->id,
            'created_by' => $this->user->id,
            'nama_barang' => 'Celana Jeans',
            'ukuran' => '32',
            'stok' => 30,
        ]);
    }

    /**
     * Helper function untuk generate kode order
     */
    protected function generateKodeOrder(): string
    {
        return 'ORD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    // ============================================
    // TEST UNTUK ORDER
    // ============================================

    /** @test */
    public function can_create_order_with_cash_payment()
    {
        // Test: Membuat order dengan pembayaran tunai (cash)
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 500000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'total_harga' => 500000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $this->assertEquals(500000, $order->total_harga);
        $this->assertEquals('bayar', $order->status);
        $this->assertEquals('cash', $order->metode_pembayaran);
        $this->assertNotNull($order->kode_order);
    }

    /** @test */
    public function can_create_order_with_non_cash_payment()
    {
        // Test: Membuat order dengan pembayaran non tunai (transfer, e-wallet, dll)
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 750000,
            'status' => 'bayar',
            'metode_pembayaran' => 'non tunai',
        ]);

        $this->assertDatabaseHas('orders', [
            'metode_pembayaran' => 'non tunai',
        ]);

        $this->assertEquals('non tunai', $order->metode_pembayaran);
    }

    /** @test */
    public function can_create_order_with_utang_status()
    {
        // Test: Membuat order dengan status utang (belum dibayar)
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 300000,
            'status' => 'utang',
            'metode_pembayaran' => 'cash',
        ]);

        $this->assertDatabaseHas('orders', [
            'status' => 'utang',
        ]);

        $this->assertEquals('utang', $order->status);
    }

    /** @test */
    public function can_create_order_with_catatan()
    {
        // Test: Membuat order dengan catatan
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 500000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
            'catatan' => 'Pesanan untuk acara pernikahan, harap dikirim sebelum tanggal 20',
        ]);

        $this->assertDatabaseHas('orders', [
            'catatan' => 'Pesanan untuk acara pernikahan, harap dikirim sebelum tanggal 20',
        ]);

        $this->assertEquals('Pesanan untuk acara pernikahan, harap dikirim sebelum tanggal 20', $order->catatan);
    }

    /** @test */
    public function can_create_order_without_catatan()
    {
        // Test: Membuat order tanpa catatan (nullable)
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 300000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'catatan' => null,
        ]);

        $this->assertNull($order->catatan);
    }

    /** @test */
    public function can_update_order_catatan()
    {
        // Test: Mengupdate catatan order
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 400000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
            'catatan' => 'Catatan awal',
        ]);

        $order->update([
            'catatan' => 'Catatan diperbarui: Harap kirim dengan bubble wrap'
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'catatan' => 'Catatan diperbarui: Harap kirim dengan bubble wrap',
        ]);
    }

    /** @test */
    public function order_has_unique_kode_order()
    {
        // Test: Setiap order memiliki kode order yang unik
        $kode1 = $this->generateKodeOrder();
        $kode2 = $this->generateKodeOrder();

        $order1 = Order::create([
            'kode_order' => $kode1,
            'user_id' => $this->user->id,
            'total_harga' => 100000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $order2 = Order::create([
            'kode_order' => $kode2,
            'user_id' => $this->user->id,
            'total_harga' => 200000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $this->assertNotEquals($order1->kode_order, $order2->kode_order);
        $this->assertNotNull($order1->kode_order);
        $this->assertNotNull($order2->kode_order);
    }

    /** @test */
    public function kode_order_format_is_correct()
    {
        // Test: Format kode order sesuai dengan pattern ORD-YYYYMMDD-XXXX
        $kodeOrder = $this->generateKodeOrder();

        $order = Order::create([
            'kode_order' => $kodeOrder,
            'user_id' => $this->user->id,
            'total_harga' => 100000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $this->assertMatchesRegularExpression(
            '/^ORD-\d{8}-\d{4}$/',
            $order->kode_order
        );

        // Verifikasi bahwa kode order mengandung tanggal hari ini
        $today = date('Ymd');
        $this->assertStringContainsString($today, $order->kode_order);
    }

    /** @test */
    public function cannot_create_order_with_duplicate_kode_order()
    {
        // Test: Tidak bisa membuat order dengan kode order yang sama (unique constraint)
        $kodeOrder = $this->generateKodeOrder();

        Order::create([
            'kode_order' => $kodeOrder,
            'user_id' => $this->user->id,
            'total_harga' => 100000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Order::create([
            'kode_order' => $kodeOrder,
            'user_id' => $this->user->id,
            'total_harga' => 200000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);
    }

    /** @test */
    public function order_belongs_to_user()
    {
        // Test: Order memiliki relasi belongsTo dengan User
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 250000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $this->assertInstanceOf(User::class, $order->user);
        $this->assertEquals($this->user->id, $order->user->id);
        $this->assertEquals('testuser', $order->user->username);
    }

    /** @test */
    public function order_has_many_order_items()
    {
        // Test: Order dapat memiliki banyak order items (relasi hasMany)
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 650000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang1->id,
            'harga_saat_order' => 250000,
            'qty' => 2,
            'subtotal' => 500000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang2->id,
            'harga_saat_order' => 150000,
            'qty' => 1,
            'subtotal' => 150000,
        ]);

        $this->assertCount(2, $order->orderItems);
        $this->assertEquals(650000, $order->orderItems->sum('subtotal'));
    }

    /** @test */
    public function deleting_user_sets_order_user_id_to_null()
    {
        // Test: When user is deleted, order's user_id is set to null
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 100000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $orderId = $order->id;

        // Gunakan forceDelete() karena User menggunakan SoftDeletes
        $this->user->forceDelete();

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'user_id' => null
        ]);
    }

    /** @test */
    public function can_create_order_without_user()
    {
        // Test: Creating order with null user_id
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => null,
            'total_harga' => 100000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => null
        ]);
    }

    /** @test */
    public function order_user_relationship_is_null_when_user_deleted()
    {
        // Test: Order's user relationship returns null when user is deleted
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 100000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $this->user->forceDelete();
        $order->refresh();

        $this->assertNull($order->user);
    }

    /** @test */
    public function can_update_order_status_from_utang_to_bayar()
    {
        // Test: Mengubah status order dari utang menjadi bayar (pelunasan)
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 400000,
            'status' => 'utang',
            'metode_pembayaran' => 'cash',
        ]);

        $order->update(['status' => 'bayar']);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'bayar',
        ]);
    }

    /** @test */
    public function order_has_timestamps()
    {
        // Test: Order memiliki created_at dan updated_at timestamps
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 100000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $this->assertNotNull($order->created_at);
        $this->assertNotNull($order->updated_at);
    }

    /** @test */
    public function total_harga_uses_decimal_precision()
    {
        // Test: Total harga menggunakan decimal dengan 2 digit presisi
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 123456.78,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $this->assertEquals('123456.78', $order->total_harga);
    }

    // ============================================
    // TEST UNTUK ORDER ITEM
    // ============================================

    /** @test */
    public function can_create_order_item()
    {
        // Test: Membuat order item dengan semua field yang diperlukan
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 500000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang1->id,
            'harga_saat_order' => 250000,
            'qty' => 2,
            'subtotal' => 500000,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'barang_id' => $this->barang1->id,
            'harga_saat_order' => 250000,
            'qty' => 2,
            'subtotal' => 500000,
        ]);
    }

    /** @test */
    public function order_item_belongs_to_order()
    {
        // Test: Order item memiliki relasi belongsTo dengan Order
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 250000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang1->id,
            'harga_saat_order' => 250000,
            'qty' => 1,
            'subtotal' => 250000,
        ]);

        $this->assertInstanceOf(Order::class, $orderItem->order);
        $this->assertEquals($order->id, $orderItem->order->id);
    }

    /** @test */
    public function order_item_belongs_to_barang()
    {
        // Test: Order item memiliki relasi belongsTo dengan Barang
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 250000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang1->id,
            'harga_saat_order' => 250000,
            'qty' => 1,
            'subtotal' => 250000,
        ]);

        $this->assertInstanceOf(Barang::class, $orderItem->barang);
        $this->assertEquals($this->barang1->id, $orderItem->barang->id);
        $this->assertEquals('Kemeja Batik', $orderItem->barang->nama_barang);
    }

    /** @test */
    public function deleting_order_deletes_order_items()
    {
        // Test: Cascade delete - ketika order dihapus, order items juga ikut terhapus
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 500000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang1->id,
            'harga_saat_order' => 250000,
            'qty' => 2,
            'subtotal' => 500000,
        ]);

        $orderItemId = $orderItem->id;

        $order->delete();

        $this->assertDatabaseMissing('order_items', [
            'id' => $orderItemId,
        ]);
    }

    /** @test */
    public function deleting_barang_deletes_order_items()
    {
        // Test: Cascade delete - ketika barang dihapus (force delete), order items yang terkait juga terhapus
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 250000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang1->id,
            'harga_saat_order' => 250000,
            'qty' => 1,
            'subtotal' => 250000,
        ]);

        $orderItemId = $orderItem->id;

        // Gunakan forceDelete() karena Barang menggunakan SoftDeletes
        $this->barang1->forceDelete();

        $this->assertDatabaseMissing('order_items', [
            'id' => $orderItemId,
        ]);
    }

    /** @test */
    public function order_item_subtotal_calculation_is_correct()
    {
        // Test: Perhitungan subtotal order item sesuai (harga × qty)
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 750000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang1->id,
            'harga_saat_order' => 250000,
            'qty' => 3,
            'subtotal' => 250000 * 3,
        ]);

        $this->assertEquals(750000, $orderItem->subtotal);
        $this->assertEquals($orderItem->harga_saat_order * $orderItem->qty, $orderItem->subtotal);
    }

    /** @test */
    public function order_item_stores_price_at_order_time()
    {
        // Test: Order item menyimpan harga pada saat order dibuat (bukan harga current)
        // Ini penting untuk histori harga
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 200000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang1->id,
            'harga_saat_order' => 200000,
            'qty' => 1,
            'subtotal' => 200000,
        ]);

        // Harga saat order berbeda dengan kondisi saat ini (frozen price)
        $this->assertEquals(200000, $orderItem->harga_saat_order);
    }

    /** @test */
    public function order_item_has_timestamps()
    {
        // Test: Order item memiliki created_at dan updated_at timestamps
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 250000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang1->id,
            'harga_saat_order' => 250000,
            'qty' => 1,
            'subtotal' => 250000,
        ]);

        $this->assertNotNull($orderItem->created_at);
        $this->assertNotNull($orderItem->updated_at);
    }

    /** @test */
    public function order_item_decimal_precision_is_correct()
    {
        // Test: Harga dan subtotal menggunakan decimal dengan 2 digit presisi
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 123.45,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang1->id,
            'harga_saat_order' => 41.15,
            'qty' => 3,
            'subtotal' => 123.45,
        ]);

        $this->assertEquals('41.15', $orderItem->harga_saat_order);
        $this->assertEquals('123.45', $orderItem->subtotal);
    }

    /** @test */
    public function can_create_multiple_order_items_for_one_order()
    {
        // Test: Satu order dapat memiliki multiple order items
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 900000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $orderItem1 = OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang1->id,
            'harga_saat_order' => 250000,
            'qty' => 2,
            'subtotal' => 500000,
        ]);

        $orderItem2 = OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang2->id,
            'harga_saat_order' => 200000,
            'qty' => 2,
            'subtotal' => 400000,
        ]);

        $this->assertDatabaseHas('order_items', ['id' => $orderItem1->id]);
        $this->assertDatabaseHas('order_items', ['id' => $orderItem2->id]);
        $this->assertCount(2, $order->fresh()->orderItems);
    }

    /** @test */
    public function order_total_equals_sum_of_order_items_subtotal()
    {
        // Test: Total harga order sama dengan jumlah subtotal dari semua order items
        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 0, // akan di-update
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang1->id,
            'harga_saat_order' => 250000,
            'qty' => 2,
            'subtotal' => 500000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang2->id,
            'harga_saat_order' => 150000,
            'qty' => 1,
            'subtotal' => 150000,
        ]);

        $totalSubtotal = $order->orderItems->sum('subtotal');
        $order->update(['total_harga' => $totalSubtotal]);

        $this->assertEquals(650000, $order->total_harga);
        $this->assertEquals($totalSubtotal, $order->total_harga);
    }

    /** @test */
    public function can_query_orders_by_status()
    {
        // Test: Query orders berdasarkan status (bayar/utang)
        $orderBayar = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 100000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $orderUtang = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 200000,
            'status' => 'utang',
            'metode_pembayaran' => 'cash',
        ]);

        $paidOrders = Order::where('status', 'bayar')->get();
        $debtOrders = Order::where('status', 'utang')->get();

        $this->assertCount(1, $paidOrders);
        $this->assertCount(1, $debtOrders);
        $this->assertTrue($paidOrders->contains($orderBayar));
        $this->assertTrue($debtOrders->contains($orderUtang));
    }

    /** @test */
    public function can_query_orders_by_payment_method()
    {
        // Test: Query orders berdasarkan metode pembayaran
        $cashOrder = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 100000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $nonCashOrder = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 200000,
            'status' => 'bayar',
            'metode_pembayaran' => 'non tunai',
        ]);

        $cashOrders = Order::where('metode_pembayaran', 'cash')->get();
        $nonCashOrders = Order::where('metode_pembayaran', 'non tunai')->get();

        $this->assertCount(1, $cashOrders);
        $this->assertCount(1, $nonCashOrders);
        $this->assertTrue($cashOrders->contains($cashOrder));
        $this->assertTrue($nonCashOrders->contains($nonCashOrder));
    }

    /** @test */
    public function can_search_orders_by_kode_order()
    {
        // Test: Mencari order berdasarkan kode order
        $kodeOrder = $this->generateKodeOrder();
        $order = Order::create([
            'kode_order' => $kodeOrder,
            'user_id' => $this->user->id,
            'total_harga' => 100000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $foundOrder = Order::where('kode_order', 'LIKE', "%{$kodeOrder}%")->first();
        $this->assertNotNull($foundOrder);
        $this->assertEquals($kodeOrder, $foundOrder->kode_order);
    }

    /** @test */
    public function can_filter_orders_by_date_range()
    {
        // Test: Filter orders berdasarkan range tanggal
        $order1 = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 100000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
            'created_at' => now()->subDays(5)
        ]);

        $order2 = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 200000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
            'created_at' => now()->subDays(2)
        ]);

        $orders = Order::whereBetween('created_at', [
            now()->subDays(3),
            now()
        ])->get();

        $this->assertCount(1, $orders);
        $this->assertTrue($orders->contains($order2));
    }

    /** @test */
    public function cannot_create_order_item_with_zero_quantity()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 100000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang1->id,
            'harga_saat_order' => 100000,
            'qty' => 0,
            'subtotal' => 0,
        ]);
    }

    /** @test */
    public function cannot_create_order_item_with_negative_price()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 100000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang1->id,
            'harga_saat_order' => -100000,
            'qty' => 1,
            'subtotal' => -100000,
        ]);
    }

    /** @test */
    public function cannot_create_order_item_exceeding_stock()
    {
        $this->barang1->update(['stok' => 5]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        $order = Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 1000000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'barang_id' => $this->barang1->id,
            'harga_saat_order' => 100000,
            'qty' => 10, // Exceeds stock of 5
            'subtotal' => 1000000,
        ]);
    }

    /** @test */
    public function can_create_bulk_orders()
    {
        $ordersData = [];
        for ($i = 0; $i < 5; $i++) {
            $ordersData[] = [
                'kode_order' => $this->generateKodeOrder(),
                'user_id' => $this->user->id,
                'total_harga' => 100000 * ($i + 1),
                'status' => 'bayar',
                'metode_pembayaran' => 'cash',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        Order::insert($ordersData);

        $this->assertEquals(5, Order::count());
    }

    /** @test */
    public function can_search_orders_by_total_price_range()
    {
        Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 100000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        Order::create([
            'kode_order' => $this->generateKodeOrder(),
            'user_id' => $this->user->id,
            'total_harga' => 500000,
            'status' => 'bayar',
            'metode_pembayaran' => 'cash',
        ]);

        $orders = Order::whereBetween('total_harga', [200000, 600000])->get();

        $this->assertCount(1, $orders);
        $this->assertEquals(500000, $orders->first()->total_harga);
    }
}
