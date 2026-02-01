<?php

namespace Tests\Feature;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_subscribe_to_plan()
    {
        $plan = Plan::factory()->create();

        $auth = $this->authenticate();

        $response = $this->withHeader(
            'Authorization',
            'Bearer '.$auth['token']
        )->postJson('/api/subscribe', [
            'plan_id' => $plan->id,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $auth['user']->id,
            'plan_id' => $plan->id,
        ]);
    }

    public function test_user_cannot_subscribe_twice()
    {
        $plan = Plan::factory()->create();
        $auth = $this->authenticate();

        $this->withHeader('Authorization', 'Bearer '.$auth['token'])
             ->postJson('/api/subscribe', ['plan_id' => $plan->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$auth['token'])
             ->postJson('/api/subscribe', ['plan_id' => $plan->id]);

        $response->assertStatus(409);
    }
}

