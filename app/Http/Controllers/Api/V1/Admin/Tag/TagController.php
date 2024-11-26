<?php

namespace App\Http\Controllers\Api\V1\Admin\Tag;

use App\Models\Tag;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::all();
        return ApiResponseHelper::success($tags, 'Tags retrieved successfully');
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
