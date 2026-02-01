<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageCounter extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'used_units',
        'cycle_start',
        'cycle_end'
    ];

    protected $casts = [
        'cycle_start' => 'datetime',
        'cycle_end' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
