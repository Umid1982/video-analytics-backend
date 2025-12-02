<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\MicroserviceException as MicroserviceExceptionAlias;
use App\Http\Controllers\Controller;
use App\Http\Requests\VideoSessions\SessionIndexRequest;
use App\Http\Requests\VideoSessions\SessionStoreRequest;
use App\Http\Resources\SessionResource;
use App\Http\Traits\ApiResponse;
use App\Http\Traits\ApiResponseHelper;
use App\Services\VideoAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    use ApiResponseHelper, ApiResponse;

    public function __construct(protected readonly VideoAnalysisService $videoAnalysisService)
    {
    }

    /**
     * @param SessionIndexRequest $request
     * @return JsonResponse
     */
    public function index(SessionIndexRequest $request): JsonResponse
    {
        return $this->paginate(SessionResource::collection(
            $this->videoAnalysisService->getUserSessions($request->filters())));
    }

    /**
     * @param SessionStoreRequest $request
     * @return JsonResponse
     * @throws MicroserviceExceptionAlias
     */
    public function store(SessionStoreRequest $request): JsonResponse
    {
        $session = $this->videoAnalysisService->createSession($request->toDTO());

        return $this->successResponse(SessionResource::make($session));
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $session = $this->videoAnalysisService->getSessionById($id, auth()->id());

        return $this->successResponse(SessionResource::make($session), 'session');
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws MicroserviceExceptionAlias
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->videoAnalysisService->deleteSession($id, auth()->id());

        return $deleted
            ? $this->successResponse([], 'data', 200, 'Session deleted successfully')
            : $this->errorResponse('Failed to delete session', 500);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws MicroserviceExceptionAlias
     */
    public function status(Request $request, int $id): JsonResponse
    {
        $session = $this->videoAnalysisService->getAnalysisStatus($id, $request->user()->id);

        return $this->successResponse(SessionResource::make($session));
    }
}

