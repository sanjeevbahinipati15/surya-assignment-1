<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'product_id',
        'quantity',
        'order_amount',
        'customer_name',
    'customer_email',
    'customer_phone',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
