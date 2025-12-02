<?php

namespace App\Services;

use App\DTOs\SessionDTO;
use App\Exceptions\MicroserviceException;
use App\HttpClients\FastApiHttpClient;
use App\Http\Traits\HasRepositories;
use App\Models\VideoSession;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Client\ConnectionException;

class VideoAnalysisService
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
     * @param SessionDTO $dto
     * @return VideoSession
     * @throws MicroserviceException
     */
    public function createSession(SessionDTO $dto): VideoSession
    {
        try {
            $response = $this->httpClient->post('/api/v1/video/analyze', [
                'source_type' => $dto->sourceType,
                'source_path' => $dto->sourcePath,
                'duration' => $dto->duration,
                'confidence_threshold' => $dto->confidenceThreshold ?? 0.5,
            ]);

            if (!$response->successful()) {
                throw MicroserviceException::fromHttpResponse(
                    $response->status(),
                    $response->body()
                );
            }

            $data = $response->json();
            $this->validateAnalyticsResponse($data);

            return $this->videoSessionRepository->createFromAnalyticsData($dto, $data);


        } catch (ConnectionException $e) {
            throw MicroserviceException::connectionError($e->getMessage());
        }
    }

    /**
     * @param array $data
     * @return void
     * @throws MicroserviceException
     */
    private function validateAnalyticsResponse(array $data): void
    {
        if (!isset($data['session_id'])) {
            throw new MicroserviceException('Invalid response: missing session_id', 500, $data);
        }
    }

    /**
     * @param int $id
     * @param int $userId
     * @return VideoSession
     * @throws MicroserviceException
     */
    public function getAnalysisStatus(int $id, int $userId): VideoSession
    {
        try {
            $session = $this->videoSessionRepository->findOrFail($id, $userId);

            $response = $this->httpClient->get("/api/v1/video/analyze/{$session->session_id}");

            if (!$response->successful()) {
                throw MicroserviceException::fromHttpResponse(
                    $response->status(),
                    $response->body()
                );
            }

            $data = $response->json();

            if (!isset($data['data'])) {
                throw new MicroserviceException('Invalid response: missing data', 500, $data);
            }

            return $this->videoSessionRepository->updateFromAnalyticsData($session, $data);

        } catch (ConnectionException $e) {
            throw MicroserviceException::connectionError($e->getMessage());
        }
    }

    /**
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getUserSessions(array $filters): LengthAwarePaginator
    {
        return $this->videoSessionRepository->findByUserWithFilters($filters);
    }

    /**
     * @param int $id
     * @param int $userId
     * @return VideoSession
     */
    public function getSessionById(int $id, int $userId): VideoSession
    {
        return $this->videoSessionRepository->findOrFail($id, $userId);
    }

    /**
     * @param int $id
     * @param int $userId
     * @return bool
     * @throws MicroserviceException
     */
    public function deleteSession(int $id, int $userId): bool
    {
        try {

            $session = $this->videoSessionRepository->findOrFail($id, $userId);
            $response = $this->httpClient->delete("/api/v1/video/sessions/{$session->session_id}");

            if (!$response->successful()) {
                throw MicroserviceException::fromHttpResponse(
                    $response->status(),
                    $response->body()
                );
            }

            $this->videoSessionRepository->delete($session);

            return true;

        } catch (ConnectionException $e) {
            throw MicroserviceException::connectionError($e->getMessage());
        }
    }
}

