<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id');
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->string('nama_lengkap', 100);
            $table->string('telepone', 100)->unique();
            $table->text('alamat');
            $table->boolean('is_super_admin')->default(false);

            $table->timestamps();    // created_at & updated_at
            $table->softDeletes();   // deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
