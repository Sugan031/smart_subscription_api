<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'monthly_limit',
        'is_unlimited'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_unlimited' => 'boolean'
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public static function getPlans() {
        return self::orderBy('price')->get();
    }
}
