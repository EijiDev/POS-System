<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'table_number', 'subtotal',
        'tax', 'total', 'payment_method', 'amount_received', 'change_given',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
