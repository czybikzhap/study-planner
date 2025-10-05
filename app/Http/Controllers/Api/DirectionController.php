<?php

namespace App\Http\Controllers\Api;

use App\DTO\SavePrioritiesDTO;
use App\Http\Requests\SavePrioritiesRequest;
use App\Http\Resources\PrioritySaveResource;
use App\Services\PriorityService;
use Illuminate\Http\JsonResponse;
class DirectionController
{
    private PriorityService $priorityService;

    public function __construct(PriorityService $priorityService)
    {
        $this->priorityService = $priorityService;
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
                'data' => new PrioritySaveResource($result)
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
