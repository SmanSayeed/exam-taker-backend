<?php

namespace App\Http\Controllers\Api\V1\Admin\Tag;

use App\Models\Tag;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request): JsonResponse
    {

        $perPage = $request->input('per_page', 99999999);

        $tags = Tag::paginate($perPage);

        return ApiResponseHelper::success($tags);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request)
    {
        $tag = Tag::create($request->validated());
        return ApiResponseHelper::success($tag, 'Tag created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag) // Route model binding
    {
        return ApiResponseHelper::success($tag, 'Tag retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag) // Route model binding
    {
        $tag->update($request->validated());
        return ApiResponseHelper::success($tag, 'Tag updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag) // Route model binding
    {
        $tag->delete();
        return ApiResponseHelper::success([], 'Tag deleted successfully');
    }
}
