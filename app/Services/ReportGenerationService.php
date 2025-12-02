<?php

namespace App\Services;

use App\DTOs\ReportDTO;
use App\Exceptions\MicroserviceException;
use App\HttpClients\FastApiHttpClient;
use App\Http\Traits\HasRepositories;
use App\Models\AnalysisReport;
use App\Models\VideoSession;
use Illuminate\Http\Client\ConnectionException;

class ReportGenerationService
{
    use HasRepositories;

    protected FastApiHttpClient $httpClient;

    public function __construct()
    {
        $this->httpClient = new FastApiHttpClient(
            config('services.fastapi.base_url'),
            config('services.fastapi.api_key')
        );
    }

    /**
     * @param ReportDTO $dto
     * @return AnalysisReport
     * @throws MicroserviceException
     */
    public function generateReport(ReportDTO $dto): AnalysisReport
    {
        try {
            $session = $this->videoSessionRepository->findBySessionId($dto->sessionId);
            $this->validateSessionAccess($session, $dto->userId);

            $response = $this->httpClient->post('/api/v1/reports/generate', [
                'session_id' => $dto->sessionId,
                'report_type' => $dto->reportType,
                'include_heatmap' => $dto->includeHeatmap,
                'include_timeline' => $dto->includeTimeline,
            ]);

            if (!$response->successful()) {
                throw MicroserviceException::fromHttpResponse(
                    $response->status(),
                    $response->body()
                );
            }

            $data = $response->json();

            return $this->analysisReportRepository->createFromReportData($session, $dto, $data);

        } catch (ConnectionException $e) {
            throw MicroserviceException::connectionError($e->getMessage());
        }
    }

    /**
     * @param VideoSession|null $session
     * @param int $userId
     * @return void
     * @throws MicroserviceException
     */
    private function validateSessionAccess(?VideoSession $session, int $userId): void
    {
        if (!$session || $session->user_id !== $userId) {
            throw new MicroserviceException('Session not found or access denied', 404);
        }
    }

    /**
     * @param string $sessionId
     * @param int $userId
     * @return array
     * @throws MicroserviceException
     */
    public function getAnalytics(string $sessionId, int $userId): array
    {
        try {
            $session = $this->videoSessionRepository->findBySessionId($sessionId);
            $this->validateSessionAccess($session, $userId);

            $response = $this->httpClient->get("/api/v1/reports/sessions/{$sessionId}/analytics");

            if (!$response->successful()) {
                throw MicroserviceException::fromHttpResponse($response->status(), $response->body());
            }

            return $response->json();

        } catch (ConnectionException $e) {
            throw MicroserviceException::connectionError($e->getMessage());
        }
    }

    /**
     * @param string $sessionId
     * @param int $userId
     * @return array
     * @throws MicroserviceException
     */
    public function getHeatmap(string $sessionId, int $userId): array
    {
        try {
            $session = $this->videoSessionRepository->findBySessionId($sessionId);
            $this->validateSessionAccess($session, $userId);

            $response = $this->httpClient->get("/api/v1/reports/sessions/{$sessionId}/heatmap");

            if (!$response->successful()) {
                throw MicroserviceException::fromHttpResponse($response->status(), $response->body());
            }

            return $response->json();

        } catch (ConnectionException $e) {
            throw MicroserviceException::connectionError($e->getMessage());
        }
    }

    /**
     * @param string $sessionId
     * @param int $userId
     * @return array
     * @throws MicroserviceException
     */
    public function getDetectionStats(string $sessionId, int $userId): array
    {
        try {
            $session = $this->videoSessionRepository->findBySessionId($sessionId);
            $this->validateSessionAccess($session, $userId);

            $response = $this->httpClient->get("/api/v1/reports/sessions/{$sessionId}/detection-stats");

            if (!$response->successful()) {
                throw MicroserviceException::fromHttpResponse($response->status(), $response->body());
            }

            return $response->json();

        } catch (ConnectionException $e) {
            throw MicroserviceException::connectionError($e->getMessage());
        }
    }

    /**
     * @param string $sessionId
     * @param int $userId
     * @return array
     * @throws MicroserviceException
     */
    public function getSummary(string $sessionId, int $userId): array
    {
        try {
            $session = $this->videoSessionRepository->findBySessionId($sessionId);
            $this->validateSessionAccess($session, $userId);

            $response = $this->httpClient->get("/api/v1/reports/sessions/{$sessionId}/summary");

            if (!$response->successful()) {
                throw MicroserviceException::fromHttpResponse($response->status(), $response->body());
            }

            return $response->json();

        } catch (ConnectionException $e) {
            throw MicroserviceException::connectionError($e->getMessage());
        }
    }
}
