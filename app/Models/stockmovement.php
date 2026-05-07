<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    /** @use HasFactory<\Database\Factories\StockMovementFactory> */
    use HasFactory;

    protected $table = 'stock_movements';

    protected $fillable = [
        'product_id',
        'quantity',
        'type',
        'user_id',
        'reference',
        'notes',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}