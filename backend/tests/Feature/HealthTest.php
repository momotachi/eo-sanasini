<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_returns_ok(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertOk()->assertJson(['status' => 'ok']);
    }

    public function test_root_redirects_to_admin(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/admin');
    }

    public function test_admin_login_page_loads(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk();
    }

    public function test_events_list_api_returns_json(): void
    {
        // seed minimal: 1 organization + 1 public event
        $org = Organization::factory()->create();
        Event::factory()->create([
            'organization_id' => $org->id,
            'is_public' => true,
        ]);

        $response = $this->getJson('/api/events');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => ['*' => ['id', 'name', 'slug', 'category', 'status']],
            ]);
    }
}
