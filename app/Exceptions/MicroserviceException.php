<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class MicroserviceException extends Exception
{
    protected int $status;
    protected ?array $responseData;

    public function __construct(
        string $message = 'Microservice error',
        int $status = 500,
        ?array $responseData = null
    ) {
        parent::__construct($message);
        $this->status = $status;
        $this->responseData = $responseData;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getResponseData(): ?array
    {
        return $this->responseData;
    }

    public static function fromHttpResponse(int $status, string $body): self
    {
        $data = json_decode($body, true);
        
        Log::channel('fastapi')->error('FastAPI error', [
            'status' => $status,
            'body' => $body,
        ]);
    
        
        return new self(
            message: $data['message'] ?? $data['error'] ?? "HTTP {$status}",
            status: $status,
            responseData: $data
        );
    }

    public static function connectionError(string $message = 'Microservice unavailable'): self
    {
        return new self($message, 503);
    }
}
