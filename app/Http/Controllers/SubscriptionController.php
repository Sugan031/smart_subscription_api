<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\UsageCounter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth('api')->user();

        if (
            Subscription::isUserSubscriptionExists($user->id)
        ) {
            return response()->json([
                'message' => 'User already has an active subscription'
            ], 409);
        }


        $plan = Plan::findOrFail($request->plan_id);

        $start = now();
        $end = now()->addMonth();

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'start_date' => $start,
            'current_cycle_start' => $start,
            'current_cycle_end' => $end,
            'status' => 'active',
        ]);

        // create first usage counter
        UsageCounter::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'used_units' => 0,
            'cycle_start' => $start,
            'cycle_end' => $end,
        ]);

        return response()->json([
            'message' => 'Subscription created successfully'
        ], 201);
    }

    public function show(Request $request)
    {
        $user = auth('api')->user();

        $subscription = $user->subscription()
            ->with('plan')
            ->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'No active subscription'
            ], 404);
    }

        return response()->json([
            'id' => $subscription->id,
            'status' => $subscription->status,
            'start_date' => $subscription->start_date,
            'current_cycle_start' => $subscription->current_cycle_start,
            'current_cycle_end' => $subscription->current_cycle_end,

            'plan' => [
                'id' => $subscription->plan->id,
                'name' => $subscription->plan->name,
                'price' => $subscription->plan->price,
                'monthly_limit' => $subscription->plan->monthly_limit,
                'is_unlimited' => $subscription->plan->is_unlimited,
            ],
        ]);
    }

    public function changePlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $user = auth('api')->user();
        $subscription = $user->subscription;

        if (! $subscription) {
            return response()->json(['message' => 'No subscription'], 404);
        }

        $newPlan = Plan::find($request->plan_id);
        $currentPlan = $subscription->plan;

        // Upgrade
        if ($newPlan->price > $currentPlan->price) {

            $start = now();
            $end = now()->addMonth();

            $subscription->update([
                'plan_id' => $newPlan->id,
                'current_cycle_start' => $start,
                'current_cycle_end' => $end,
            ]);

            UsageCounter::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'used_units' => 0,
                'cycle_start' => $start,
                'cycle_end' => $end,
            ]);

            return response()->json([
                'message' => 'Plan upgraded successfully'
            ]);
        }
        // Downgrade(will work on next cycle)
        $subscription->update([
            'next_plan_id' => $newPlan->id
        ]);

        return response()->json([
            'message' => 'Plan downgrade scheduled for next cycle'
        ]);
    }

}
