<?php

namespace App\Repositories;

use App\DTOs\SessionDTO;
use App\Models\VideoSession;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VideoSessionRepository
{

    /**
     * @param SessionDTO $dto
     * @param array $analyticsData
     * @return VideoSession
     */
    public function createFromAnalyticsData(SessionDTO $dto, array $analyticsData): VideoSession
    {
        return VideoSession::query()->create([
            'user_id' => $dto->userId,
            'session_id' => $analyticsData['session_id'],
            'status' => $analyticsData['status'] ?? 'processing',
            'source_type' => $dto->sourceType,
            'source_path' => $dto->sourcePath,
            'start_time' => now(),
        ]);
    }

    /**
     * @param int $id
     * @param int $userId
     * @return VideoSession
     */
    public function findOrFail(int $id, int $userId): VideoSession
    {
        return VideoSession::query()->where('user_id', $userId)->findOrFail($id);
    }

    /**
     * @param VideoSession $session
     * @param array $analyticsData
     * @return VideoSession
     */
    public function updateFromAnalyticsData(VideoSession $session, array $analyticsData): VideoSession
    {
        $updateData = [
            'status' => $analyticsData['data']['status'] ?? $session->status,
            'total_frames' => $analyticsData['data']['total_frames'] ?? 0,
            'total_people' => $analyticsData['data']['total_people'] ?? 0,
            'peak_people_count' => $analyticsData['data']['peak_people_count'] ?? 0,
            'average_stay_time' => $analyticsData['data']['average_stay_time'] ?? null,
            'metadata' => array_merge(
                $session->metadata ?? [],
                [
                    'last_status_check' => now()->toISOString(),
                    'raw_response' => $analyticsData
                ]
            ),
            'start_time' => $analyticsData['data']['start_time'] ?? $session->start_time,
            'end_time' => $analyticsData['data']['end_time'] ?? $session->end_time,
        ];

        $session->update($updateData);
        return $session->refresh();
    }

    /**
     * @param string $sessionId
     * @return VideoSession|null
     */
    public function findBySessionId(string $sessionId): ?VideoSession
    {
        return VideoSession::query()->where('session_id', $sessionId)->first();
    }

    /**
     * @param VideoSession $session
     * @return bool
     */
    public function delete(VideoSession $session): bool
    {
        return $session->delete();
    }

    /**
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function findByUserWithFilters(array $filters = []): LengthAwarePaginator
    {
        $query = VideoSession::query()
            ->where('user_id', $filters['user_id'])
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['source_type']), fn($q) => $q->where('source_type', $filters['source_type']))
            ->when(isset($filters['date_from']), fn($q) => $q->where('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn($q) => $q->where('created_at', '<=', $filters['date_to']))
            ->when(isset($filters['order_by']), function ($q) use ($filters) {
                $dir = $filters['order_dir'] ?? 'desc';
                $q->orderBy($filters['order_by'], $dir);
            }, fn($q) => $q->orderBy('created_at', 'desc'));

        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }
}

