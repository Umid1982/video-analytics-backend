<?php

namespace Tests\Unit;

use App\HttpClients\FastApiHttpClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FastApiHttpClientTest extends TestCase
{
    public function test_get_includes_api_key_header(): void
    {
        $client = new FastApiHttpClient('https://example.test', 'api-key-123');

        Http::fake([
            'https://example.test/*' => Http::response(['ok' => true], 200),
        ]);

        $captured = null;
        Http::assertSent(function ($request) use (&$captured) {
            $captured = $request;
            return true;
        });

        $client->get('/ping');

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-API-KEY', 'api-key-123');
        });
    }
}


