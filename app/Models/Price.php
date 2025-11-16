<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Price extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'harga',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
