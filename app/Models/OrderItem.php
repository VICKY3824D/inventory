<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'order_id',
        'barang_id',
        'harga_saat_order',
        'qty',
        'subtotal'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderItem) {
            if ($orderItem->qty <= 0) {
                throw new \Exception('Quantity must be greater than 0');
            }
        });

        static::updating(function ($orderItem) {
            if ($orderItem->qty <= 0) {
                throw new \Exception('Quantity must be greater than 0');
            }
        });

        static::creating(function ($orderItem) {
            if ($orderItem->harga_saat_order<= 0) {
                throw new \Exception('Harga must be greater than 0');
            }
        });

        static::updating(function ($orderItem) {
            if ($orderItem->harga_saat_order <= 0) {
                throw new \Exception('Harga must be greater than 0');
            }
        });
    }

    /**
     * Get the order that owns the order item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Order, \App\Models\OrderItem>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the barang that owns the order item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Barang, \App\Models\OrderItem>
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
