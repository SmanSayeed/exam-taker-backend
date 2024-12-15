<?php
namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GalleryController extends Controller
{
    // Upload image directly to the public folder
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

        // Log temporary file details
        \Log::info('Temporary file path: ' . $image->getPathname()); // This will log the temp file path

        // Ensure the custom name includes the file extension
        $extension = $image->getClientOriginalExtension();
        $imageName = $request->input('image_name')
            ? pathinfo($request->input('image_name'), PATHINFO_FILENAME) . '.' . $extension
            : time() . '.' . $extension;

        try {
            // Move image to public/images folder
            $imagePath = $image->move(public_path('images'), $imageName);

            // Save image data in the database
            $imageData = new Gallery();
            $imageData->image_path = 'images/' . $imageName; // Store relative path in DB
            $imageData->image_name = $imageName;
            $imageData->image_size = 1; //$image->getSize();
            $imageData->image_format = $extension;
            $imageData->save();

            // Generate full URL for the image (directly accessible)
            $imageFullUrl = url('images/' . $imageName);

            return ApiResponseHelper::success([
                'id' => $imageData->id,
                'image_name' => $imageData->image_name,
                'image_url' => $imageFullUrl, // Full URL for the image
                'image_size' => $imageData->image_size ,
                'image_format' => $imageData->image_format
            ], 'Image uploaded successfully', 201);
        } catch (\Exception $e) {
            // Log any errors
            \Log::error('File upload error: ' . $e->getMessage());

            return ApiResponseHelper::error('Image upload failed: ' . $e->getMessage(), 500);
        }
    }

    return ApiResponseHelper::error('No image uploaded', 400);
}


 public function updateImage(Request $request, $id)
{
    $imageData = Gallery::find($id);

    if (!$imageData) {
        return ApiResponseHelper::error('Image not found', 404);
    }

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
        // Delete the old image from the public/images folder
        $oldImagePath = public_path($imageData->image_path);
        if (file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }

        $image = $request->file('image');

        // Ensure the custom name includes the file extension
        $extension = $image->getClientOriginalExtension();
        $newImageName = $request->input('image_name')
            ? pathinfo($request->input('image_name'), PATHINFO_FILENAME) . '.' . $extension
            : $imageData->image_name;

        // Save the image to the public/images folder directly
        $imagePath = $image->move(public_path('images'), $newImageName);

        // Update image data in the database
        $imageData->image_path = 'images/' . $newImageName; // Store relative path in DB
        $imageData->image_name = $newImageName;
        $imageData->image_size = 1; //$image->getSize();
        $imageData->image_format = $extension;
        $imageData->save();

        // Generate full URL for the updated image (directly accessible)
        $imageFullUrl = url('images/' . $newImageName);

        return ApiResponseHelper::success([
            'id' => $imageData->id,
            'image_name' => $imageData->image_name,
            'image_url' => $imageFullUrl, // Full URL for the updated image
            'image_size' => $imageData->image_size,
            'image_format' => $imageData->image_format
        ], 'Image updated successfully', 200);
    }

    return ApiResponseHelper::error('No image uploaded', 400);
}


    // Get all images
    public function getImages()
    {
        $images = Gallery::all();

        // Add the full URL for each image
        foreach ($images as $image) {
            $image->image_url = url(str_replace('public', 'storage', $image->image_path));
        }

        return ApiResponseHelper::success($images, 'Images fetched successfully', 200);
    }

    // Get a single image by ID
    public function getImageById($id)
    {
        $image = Gallery::find($id);

        if (!$image) {
            return ApiResponseHelper::error('Image not found', 404);
        }

        // Add the full URL for the image
        $image->image_url = url(str_replace('public', 'storage', $image->image_path));

        return ApiResponseHelper::success([
            'id' => $image->id,
            'image_name' => $image->image_name,
            'image_url' => $image->image_url, // Full URL for the image
            'image_size' => $image->image_size,
            'image_format' => $image->image_format
        ], 'Image fetched successfully', 200);
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
}
