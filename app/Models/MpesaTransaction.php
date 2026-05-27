<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[fillable(['checkout_request_id',
    'merchant_request_id',
    'phone',
    'amount',
    'status',
    'mpesa_receipt_number',
    'cart_items',
    'callback_payload',])]

class MpesaTransaction extends Model
{
    

protected $casts = [
    'cart_items' => 'array',
    'callback_payload' => 'array',
];
    //
}
