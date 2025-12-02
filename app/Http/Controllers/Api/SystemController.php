<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\HttpClients\FastApiHttpClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Client\ConnectionException;

class SystemController extends Controller
{
    protected FastApiHttpClient $httpClient;

    public function __construct()
    {
        $this->httpClient = new FastApiHttpClient(
            config('services.fastapi.base_url'),
            config('services.fastapi.api_key')
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ConnectionException
     */
    public function root(Request $request): JsonResponse
    {
        return response()->json(
            $this->httpClient->get('/')->json());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ConnectionException
     */
    public function health(Request $request): JsonResponse
    {
        return response()->json(
            $this->httpClient->get('/health')->json());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ConnectionException
     */
    public function info(Request $request): JsonResponse
    {
        return response()->json(
            $this->httpClient->get('/info')->json());
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ConnectionException
     */
    public function metrics(Request $request): Response
    {
        $response = $this->httpClient->get('/metrics');
        
        return response($response->body(), $response->status())
            ->header('Content-Type', 'text/plain; version=0.0.4; charset=utf-8');
    }
}

