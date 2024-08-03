<?php

namespace App\Http\Controllers\Api\V1\Questions;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponseHelper;
use App\Services\Question\QuestionBaseService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Api\V1\Questions\QuestionBaseRequest;
use App\DTOs\QuestionDTO\QuestionEntityData;
use Exception;

abstract class QuestionBaseController extends Controller
{
    protected QuestionBaseService $service;

    public function __construct(QuestionBaseService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        try {
            $items = $this->service->getAll();
            return ApiResponseHelper::success($items);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to retrieve items: ' . $e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $item = $this->service->findById($id);
            if ($item) {
                return ApiResponseHelper::success($item);
            }
            return ApiResponseHelper::error('Item not found', 404);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to retrieve item: ' . $e->getMessage());
        }
    }

    public function store(QuestionBaseRequest $request): JsonResponse
    {
        try {
            $data = $this->getDtoFromRequest($request);
            $item = $this->service->create($data->toArray());
            return ApiResponseHelper::success($item, 'Item created successfully', 201);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to create item: ' . $e->getMessage());
        }
    }

    public function update(int $id, QuestionBaseRequest $request): JsonResponse
    {
        try {
            $data = $this->getDtoFromRequest($request);
            $updated = $this->service->update($id, $data->toArray());
            if ($updated) {
                return ApiResponseHelper::success(null, 'Item updated successfully');
            }
            return ApiResponseHelper::error('Item not found', 404);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to update item: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->service->delete($id);
            if ($deleted) {
                return ApiResponseHelper::success(null, 'Item deleted successfully');
            }
            return ApiResponseHelper::error('Item not found', 404);
        } catch (Exception $e) {
            return ApiResponseHelper::error('Failed to delete item: ' . $e->getMessage());
        }
    }

    abstract protected function getDtoFromRequest(QuestionBaseRequest $request): QuestionEntityData;
}
