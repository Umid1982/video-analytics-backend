<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_metrics_returns_text_plain(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $metricsText = "# HELP app_requests_total Total requests\n# TYPE app_requests_total counter\napp_requests_total 1";

        Http::fake([
            config('services.fastapi.base_url').'/metrics' => Http::response($metricsText, 200, [
                'Content-Type' => 'text/plain; version=0.0.4; charset=utf-8'
            ]),
        ]);

        $response = $this->get('/api/system/metrics');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; version=0.0.4; charset=utf-8');
        $this->assertStringContainsString('app_requests_total', $response->getContent());
    }
}


