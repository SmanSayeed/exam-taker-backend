<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GalleryController extends Controller
{
    public function uploadImage(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_name' => 'nullable|string|max:255', // Optional image name
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::error('Validation failed', 422, $validator->errors());
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $request->input('image_name') ?? time() . '.' . $image->getClientOriginalExtension(); // Use provided name or generate one
            $imagePath = $image->storeAs('public/images', $imageName);

            $imageData = new Gallery();
            $imageData->image_path = $imagePath;
            $imageData->image_name = $imageName;
            $imageData->image_size = $image->getSize();
            $imageData->image_format = $image->getClientOriginalExtension();
            $imageData->save();

            return ApiResponseHelper::success($imageData, 'Image uploaded successfully', 201);
        }

        return ApiResponseHelper::error('No image uploaded', 400);
    }

    // Update image
    public function updateImage(Request $request, $id)
    {
        $imageData = Gallery::find($id);

        if (!$imageData) {
            return ApiResponseHelper::error('Image not found', 404);
        }

        // Log the received data
        logger()->info('Request received for image update', [
            'id' => $id,
            'request' => $request->all(),
        ]);

        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_name' => 'nullable|string|max:255', // Optional image name
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::error('Validation failed', 422, $validator->errors());
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete the old image
            Storage::delete($imageData->image_path);

            $image = $request->file('image');
            $newImageName = $request->input('image_name') ?? $imageData->image_name; // Use provided name or keep the existing one
            $imagePath = $image->storeAs('public/images', $newImageName);

            $imageData->image_path = $imagePath;
            $imageData->image_name = $newImageName;
            $imageData->image_size = $image->getSize();
            $imageData->image_format = $image->getClientOriginalExtension();
            $imageData->save();

            return ApiResponseHelper::success($imageData, 'Image updated successfully', 200);
        }

        return ApiResponseHelper::error('No image uploaded', 400);
    }


    // Delete image
    public function deleteImage($id)
    {
        $imageData = Gallery::find($id);

        if (!$imageData) {
            return ApiResponseHelper::error('Image not found', 404);
        }

        // Delete the image file from storage
        Storage::delete($imageData->image_path);
        $imageData->delete();

        return ApiResponseHelper::success([], 'Image deleted successfully', 200);
    }

    // Get all images
    public function getImages()
    {
        $images = Gallery::all();
        return ApiResponseHelper::success($images, 'Images fetched successfully', 200);
    }
}
