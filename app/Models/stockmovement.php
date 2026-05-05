<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['product_id', 'quantity', 'movement_type', 'user_id'])]

class stockmovement extends Model
{
    /** @use HasFactory<\Database\Factories\StockmovementFactory> */
    use HasFactory;

    protected $table = 'stock_movements';
}
