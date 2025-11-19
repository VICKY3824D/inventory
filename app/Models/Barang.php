<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'nama_barang',
        'ukuran',
        'stok',
        'warna',
        'img',
        'deskripsi',
        'created_by',
    ];

    protected $dates = ['deleted_at'];

    public function kategori()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Tambahkan relasi ke Price (harga terbaru)
    public function latestPrice(): HasOne
    {
        return $this->hasOne(Price::class, 'product_id')->latestOfMany();
    }

    // Atau untuk semua history harga
    public function prices(): HasMany
    {
        return $this->hasMany(Price::class, 'product_id');
    }
}
