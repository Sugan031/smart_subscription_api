<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\UsageCounter;
use Illuminate\Console\Command;

class RenewSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used to reset or renew the subscriptions/billing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Subscription::where('status', 'active')
        ->whereDate('current_cycle_end', '<', now())
        ->each(function ($sub) {

            $start = now();
            $end = now()->addMonth();

            // Update subscription cycle
            $sub->update([
                'current_cycle_start' => $start,
                'current_cycle_end' => $end,
            ]);

            
            if ($sub->next_plan_id) {
                $sub->update([
                    'plan_id' => $sub->next_plan_id,
                    'next_plan_id' => null,
                ]);
            }

            // ✅ CREATE a new usage counter row
            UsageCounter::create([
                'user_id' => $sub->user_id,
                'subscription_id' => $sub->id,
                'used_units' => 0,
                'cycle_start' => $start,
                'cycle_end' => $end,
            ]);
        });
    }
}
