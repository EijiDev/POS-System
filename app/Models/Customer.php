<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'points', 'total_visits', 'total_spent', 'tier'];

    protected $casts = ['total_spent' => 'float', 'points' => 'integer', 'total_visits' => 'integer'];

    public static function tierFromPoints(int $points): string
    {
        if ($points >= 500) return 'Gold';
        if ($points >= 100) return 'Silver';
        return 'Bronze';
    }

    public static function discountFromTier(string $tier): float
    {
        return match($tier) {
            'Gold'   => 0.10,
            'Silver' => 0.05,
            default  => 0.00,
        };
    }
}
