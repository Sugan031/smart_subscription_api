<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsageController extends Controller
{
    public function consume(Request $request)
    {
        $user = auth('api')->user();
        $subscription = $user->subscription;

        if (! $subscription || $subscription->status !== 'active') {
            return response()->json([
                'message' => 'No active subscription'
            ], 403);
        }

        $usage = $subscription->usageCounter;
        $plan = $subscription->plan;

        // unlimited plan → always allow
        if (! $plan->is_unlimited) {
            if ($usage->used_units >= $plan->monthly_limit) {
                return response()->json([
                    'message' => 'Usage limit exceeded'
                ], 429);
            }
        }

        $usage->increment('used_units');

        return response()->json([
            'message' => 'Usage consumed successfully'
        ]);
    }

    public function stats(Request $request)
    {
        $user = auth('api')->user();

        $subscription = $user->subscription;

        if (! $subscription || $subscription->status !== 'active') {
            return response()->json([
                'message' => 'No active subscription'
            ], 404);
        }

        $usage = $subscription->usageCounter;
        $plan = $subscription->plan;

        if (! $usage) {
            return response()->json([
                'message' => 'Usage data not found'
            ], 404);
        }

        return response()->json([
            'cycle_start' => $usage->cycle_start,
            'cycle_end' => $usage->cycle_end,
            'used_units' => $usage->used_units,

            'limit' => $plan->is_unlimited ? 'unlimited' : $plan->monthly_limit,
            'remaining_units' => $plan->is_unlimited
                ? 'unlimited'
                : max($plan->monthly_limit - $usage->used_units, 0),
        ]);
    }
}
