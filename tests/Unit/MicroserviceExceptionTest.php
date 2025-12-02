<?php

namespace Tests\Unit;

use App\Exceptions\MicroserviceException;
use Tests\TestCase;

class MicroserviceExceptionTest extends TestCase
{
    public function test_from_http_response_parses_status_and_message(): void
    {
        $status = 422;
        $body = json_encode(['error' => 'Validation failed']);

        $ex = MicroserviceException::fromHttpResponse($status, $body);

        $this->assertInstanceOf(MicroserviceException::class, $ex);
        $this->assertSame($status, $ex->getStatus());
        $this->assertSame('Validation failed', $ex->getMessage());
        $this->assertIsArray($ex->getResponseData());
        $this->assertSame('Validation failed', $ex->getResponseData()['error']);
    }
}


