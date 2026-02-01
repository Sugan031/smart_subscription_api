<?php

namespace Tests\Feature;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_user_cannot_access_plans()
    {
        $this->getJson('/api/plans')
             ->assertStatus(401)
             ->assertJson([
                 'message' => 'Unauthorized',
             ]);
    }

    /** @test */
    public function authenticated_user_can_get_plans()
    {
        Plan::factory()->count(3)->create();

        $auth = $this->authenticate();

        $response = $this->withHeader(
            'Authorization',
            'Bearer ' . $auth['token']
        )->getJson('/api/plans');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function plans_response_has_expected_structure()
    {
        Plan::factory()->create([
            'name' => 'Pro',
            'price' => 999,
            'monthly_limit' => 10000,
            'is_unlimited' => false,
        ]);

        $auth = $this->authenticate();

        $this->withHeader(
            'Authorization',
            'Bearer ' . $auth['token']
        )->getJson('/api/plans')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'name',
                    'price',
                    'monthly_limit',
                    'is_unlimited',
                ]
            ]
        ]);
    }
}
