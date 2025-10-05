<?php

namespace App\Http\Controllers;

use App\DTO\SavePrioritiesDTO;
use App\Http\Requests\SavePrioritiesRequest;
use App\Services\PriorityService;
use Illuminate\Http\JsonResponse;

class DirectionController extends Controller
{
    private PriorityService $priorityService;

    public function __construct(PriorityService $priorityService)
    {
        $this->priorityService = $priorityService;
    }

    public function index()
    {
        $directions = $this->priorityService->getAllDirectionsWithProfiles();
        return view('directions.index', compact('directions'));
    }

    public function savePriorities(SavePrioritiesRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $userId = 1;

            $dto = SavePrioritiesDTO::fromRequest($data, $userId);
            $result = $this->priorityService->saveUserPriorities($dto);

            return response()->json([
                'success' => true,
                'message' => 'Приоритеты сохранены!',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            logger()->error('Save error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка сохранения: ' . $e->getMessage()
            ], 500);
        }
    }


}
