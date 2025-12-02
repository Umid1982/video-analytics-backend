<?php

namespace Tests\Feature;

use App\Models\AnalysisReport;
use App\Models\User;
use App\Models\VideoSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    private function baseUrl(): string
    {
        return config('services.fastapi.base_url');
    }

    public function test_generate_report_creates_analysis_report_record(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $session = VideoSession::query()->create([
            'user_id' => $user->id,
            'session_id' => (string) Str::uuid(),
            'status' => 'completed',
            'source_type' => 'file',
            'source_path' => '/p/video.mp4',
        ]);

        $fakeResponse = [
            'analytics' => [
                'session_id' => $session->session_id,
                'total_frames' => 300,
                'total_people' => 5,
                'peak_people_count' => 5,
                'average_stay_time' => 1.7,
                'heatmap_points' => [
                    ['x' => 1, 'y' => 2, 'intensity' => 0.5, 'timestamp' => now()->toISOString()],
                ],
            ],
            'summary' => 'AI summary text',
        ];

        Http::fake([
            $this->baseUrl().'/api/v1/reports/generate' => Http::response($fakeResponse, 200),
        ]);

        $payload = [
            'session_id' => $session->session_id,
            'report_type' => 'summary',
            'include_heatmap' => true,
            'include_timeline' => true,
        ];

        $response = $this->postJson('/api/reports/generate', $payload);
        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('analysis_reports', [
            'session_id' => $session->id,
            'user_id' => $user->id,
            'report_type' => 'summary',
        ]);

        /** @var AnalysisReport $report */
        $report = AnalysisReport::query()->first();
        $this->assertNotNull($report);
        $this->assertEquals($fakeResponse['analytics'], $report->analytics_data);
        $this->assertEquals($fakeResponse['analytics']['heatmap_points'], $report->heatmap_data);
        $this->assertEquals('AI summary text', $report->ai_generated_summary);
    }
}


