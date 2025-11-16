<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('created_by');
            $table->string('nama_barang', 200);
            $table->string('ukuran', 100)->nullable();
            $table->integer('stok')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by', 'fk_barang_created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
