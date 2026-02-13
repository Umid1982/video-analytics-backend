<?php

namespace App\Services;

use App\Exceptions\MicroserviceException;
use App\HttpClients\FastApiHttpClient;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class HelmetViolationService
{
    protected FastApiHttpClient $httpClient;

    protected string $botToken;
    protected string $chatId;

    public function __construct()
    {
        $this->httpClient = new FastApiHttpClient(
            config('services.fastapi.base_url'),
            config('services.fastapi.api_key')
        );

        $this->botToken = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
    }

    /**
     * @param int $limit
     * @return array
     * @throws MicroserviceException
     */
    public function getUnsent(int $limit = 50): array
    {
        try {
            $response = $this->httpClient->get('/api/v1/violations/unsent', [
                'limit' => $limit
            ]);

            if (!$response->successful()) {
                throw MicroserviceException::fromHttpResponse(
                    $response->status(),
                    $response->body()
                );
            }

            return $response->json();

        } catch (ConnectionException $e) {
            throw MicroserviceException::connectionError($e->getMessage());
        }
    }

    /**
     * @param array $ids
     * @return void
     * @throws MicroserviceException
     */
    public function markAsSent(array $ids): void
    {
        try {
            $response = $this->httpClient->post('/api/v1/violations/mark-sent', [
                'violation_ids' => $ids
            ]);

            if (!$response->successful()) {
                throw MicroserviceException::fromHttpResponse(
                    $response->status(),
                    $response->body()
                );
            }

        } catch (ConnectionException $e) {
            throw MicroserviceException::connectionError($e->getMessage());
        }
    }

    /**
     * @param array $violation
     * @return void
     * @throws ConnectionException
     */
    public function sendViolation(array $violation): void
    {
        $text = "🚨 Нарушение каски!\n\n"
            . "Камера: {$violation['camera_id']}\n"
            . "Время: {$violation['timestamp']}\n"
            . "Confidence: {$violation['confidence']}";

        $imageResponse = $this->httpClient->get('/' . $violation['image_path']);

        if ($imageResponse->successful()) {

            $telegramResponse = Http::timeout(30)
                ->retry(3, 500) // 3 попытки, пауза 500мс
                ->attach(
                    'photo',
                    $imageResponse->body(),
                    'violation.jpg'
                )
                ->post("https://api.telegram.org/bot{$this->botToken}/sendPhoto", [
                    'chat_id' => $this->chatId,
                    'caption' => $text,
                ]);

            if (!$telegramResponse->successful()) {
                throw new \Exception('Telegram sendPhoto failed: ' . $telegramResponse->body());
            }
        }
    }
}
