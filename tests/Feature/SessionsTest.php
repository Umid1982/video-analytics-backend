<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VideoSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SessionsTest extends TestCase
{
    use RefreshDatabase;

    private function fakeBaseUrl(): string
    {
        return config('services.fastapi.base_url');
    }

    public function test_store_creates_session_with_fastapi_success(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $sessionId = (string) Str::uuid();

        Http::fake([
            $this->fakeBaseUrl().'/api/v1/video/analyze' => Http::response([
                'session_id' => $sessionId,
                'status' => 'processing',
            ], 200),
        ]);

        $payload = [
            'source_type' => 'file',
            'source_path' => '/path/to/video.mp4',
            'duration' => 60,
            'confidence_threshold' => 0.5,
        ];

        $response = $this->postJson('/api/sessions', $payload);
        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('video_sessions', [
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'status' => 'processing',
        ]);
    }

    public function test_status_returns_updated_session_fields(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $session = VideoSession::query()->create([
            'user_id' => $user->id,
            'session_id' => (string) Str::uuid(),
            'status' => 'processing',
            'source_type' => 'file',
            'source_path' => '/path/file.mp4',
        ]);

        Http::fake([
            $this->fakeBaseUrl().'/api/v1/video/analyze/'.$session->session_id => Http::response([
                'data' => [
                    'status' => 'completed',
                    'total_frames' => 100,
                    'total_people' => 5,
                    'peak_people_count' => 3,
                    'start_time' => now()->subMinutes(5)->toISOString(),
                    'end_time' => now()->toISOString(),
                ],
            ], 200),
        ]);

        $response = $this->getJson('/api/sessions/'.$session->id.'/status');
        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('video_sessions', [
            'id' => $session->id,
            'user_id' => $user->id,
            'status' => 'completed',
            'total_frames' => 100,
            'total_people' => 5,
            'peak_people_count' => 3,
        ]);
    }

    public function test_destroy_deletes_session_and_calls_fastapi(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $session = VideoSession::query()->create([
            'user_id' => $user->id,
            'session_id' => (string) Str::uuid(),
            'status' => 'completed',
            'source_type' => 'file',
            'source_path' => '/path/file.mp4',
        ]);

        Http::fake([
            $this->fakeBaseUrl().'/api/v1/video/sessions/'.$session->session_id => Http::response([], 200),
        ]);

        $response = $this->deleteJson('/api/sessions/'.$session->id);
        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseMissing('video_sessions', [
            'id' => $session->id,
        ]);
    }
}


