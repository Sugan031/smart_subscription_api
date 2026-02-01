<?php

namespace Tests\Feature;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsageTest extends TestCase
{
    use RefreshDatabase;

    public function test_usage_is_consumed()
    {
        $plan = Plan::factory()->create([
            'monthly_limit' => 5,
            'is_unlimited' => false,
        ]);

        $auth = $this->authenticate();

        $this->withHeader('Authorization', 'Bearer '.$auth['token'])
             ->postJson('/api/subscribe', ['plan_id' => $plan->id]);

        $response = $this->withHeader(
            'Authorization',
            'Bearer '.$auth['token']
        )->postJson('/api/usage/consume');

        $response->assertStatus(200);

        $this->assertDatabaseHas('usage_counters', [
            'used_units' => 1,
        ]);
    }
}

