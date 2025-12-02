<?php declare(strict_types=1);

namespace App\DTOs;

final readonly class ReportDTO
{
    public function __construct(
        public int    $userId,
        public string $sessionId,
        public string $reportType,
        public bool   $includeHeatmap = false,
        public bool   $includeTimeline = false,
    )
    {
    }
}