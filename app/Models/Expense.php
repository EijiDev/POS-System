<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['description', 'category', 'amount', 'date', 'status'];

    protected $casts = ['amount' => 'float', 'date' => 'date'];
}
