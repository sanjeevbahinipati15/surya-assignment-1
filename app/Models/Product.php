<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'code',
        'price',
        'selling_price',
        'quantity',
        'image',
        'minimum_order_quantity',
        'max_order_quantity',
        'discount',
        'discount_in_percentage',
        'status',
    ];
}
