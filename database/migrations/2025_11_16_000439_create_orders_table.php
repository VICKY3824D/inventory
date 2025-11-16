<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_order')->unique();
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('total_harga', 15, 2);
            $table->enum('status', ['bayar', 'utang']);
            $table->enum('metode_pembayaran', ['cash', 'non tunai']);
            $table->text('catatan')->nullable();

            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
