<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'price' => 0,
                'monthly_limit' => 100,
                'is_unlimited' => false,
            ],
            [
                'name' => 'Pro',
                'price' => 999,
                'monthly_limit' => 10000,
                'is_unlimited' => false,
            ],
            [
                'name' => 'Enterprise',
                'price' => 4999,
                'monthly_limit' => null,
                'is_unlimited' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['name' => $plan['name']], // unique key
                $plan
            );
        }
    }
}
