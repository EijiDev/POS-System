<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'category', 'price', 'cost', 'stock', 'sold', 'status', 'img'];

    protected $casts = ['price' => 'float', 'cost' => 'float', 'stock' => 'integer', 'sold' => 'integer'];
}
