<?php

namespace App\Repositories;

use App\DTOs\ReportDTO;
use App\Models\AnalysisReport;
use App\Models\VideoSession;

class AnalysisReportRepository
{
    /**
     * @param VideoSession $session
     * @param ReportDTO $dto
     * @param array $externalData
     * @return AnalysisReport
     */
    public function createFromReportData(
        VideoSession $session,
        ReportDTO    $dto,
        array        $externalData
    ): AnalysisReport
    {
        return AnalysisReport::query()->create([
            'session_id' => $session->id,
            'user_id' => $session->user_id,
            'report_type' => $dto->reportType,
            'analytics_data' => $externalData['analytics'] ?? null,
            'heatmap_data' => $externalData['analytics']['heatmap_points'] ?? null,
            'ai_generated_summary' => $externalData['summary'] ?? null,
            'generated_at' => now(),
        ]);
    }
}
