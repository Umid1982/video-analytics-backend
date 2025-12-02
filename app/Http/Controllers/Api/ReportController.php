<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\MicroserviceException as MicroserviceExceptionAlias;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\ReportStoreRequest;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\ApiResponseHelper;
use App\Services\ReportGenerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use ApiResponseHelper, ApiResponse;

    public function __construct(protected readonly ReportGenerationService $reportService)
    {
    }

    /**
     * @param ReportStoreRequest $request
     * @return JsonResponse
     * @throws MicroserviceExceptionAlias
     */
    public function store(ReportStoreRequest $request): JsonResponse
    {
        $report = $this->reportService->generateReport($request->toDTO());

        return $this->successResponse($report);
    }

    /**
     * @param Request $request
     * @param string $sessionId
     * @return JsonResponse
     * @throws MicroserviceExceptionAlias
     */
    public function analytics(Request $request, string $sessionId): JsonResponse
    {
        return response()->json(
            $this->reportService->getAnalytics($sessionId, $request->user()->id));
    }

    /**
     * @param Request $request
     * @param string $sessionId
     * @return JsonResponse
     * @throws MicroserviceExceptionAlias
     */
    public function heatmap(Request $request, string $sessionId): JsonResponse
    {
        return response()->json(
            $this->reportService->getHeatmap($sessionId, $request->user()->id));
    }

    /**
     * @param Request $request
     * @param string $sessionId
     * @return JsonResponse
     * @throws MicroserviceExceptionAlias
     */
    public function detectionStats(Request $request, string $sessionId): JsonResponse
    {
        return response()->json(
            $this->reportService->getDetectionStats($sessionId, $request->user()->id));
    }

    /**
     * @param Request $request
     * @param string $sessionId
     * @return JsonResponse
     * @throws MicroserviceExceptionAlias
     */
    public function summary(Request $request, string $sessionId): JsonResponse
    {
        return response()->json(
            $this->reportService->getSummary($sessionId, $request->user()->id));
    }
}
