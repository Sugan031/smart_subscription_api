<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'start_date',
        'current_cycle_start',
        'current_cycle_end',
        'next_plan_id',
        'status'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'current_cycle_start' => 'datetime',
        'current_cycle_end' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function usageCounter()
    {
        return $this->hasOne(UsageCounter::class)
            ->where('cycle_end', '>=', now());
    }

    public static function isUserSubscriptionExists($userId) {
       return self::where('user_id', $userId)
                ->where('status', 'active')
                ->exists();
    }
}
