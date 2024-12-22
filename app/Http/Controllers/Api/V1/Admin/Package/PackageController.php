<?php

namespace App\Http\Controllers\Api\V1\Admin\Package;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use App\Helpers\ApiResponseHelper;
use App\Http\Requests\AttachPackageTagRequest;
use App\Http\Requests\AttachPdfRequest;
use App\Http\Requests\DetachPackageTagRequest;
use App\Http\Requests\DetachPdfRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Package\UpdatePackageRequest;
use App\Http\Requests\Package\ChangePackageStatusRequest;
use App\Http\Requests\Package\StorePackageRequest;
use App\Http\Requests\PackageIndexRequest;
use App\Http\Resources\PackageAdminResource;
use App\Http\Resources\StudentResource\StudentResource;
use App\Models\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function index(PackageIndexRequest $request): JsonResponse
    {
        // Get per_page value from request or set default to 15
        $perPage = $request->get('per_page', 15);

        // Initialize the query
        $query = Package::query();

        // Check for filtering parameters
        if ($request->has('section_id')) {
            $query->where('section_id', $request->input('section_id'));
        }
        if ($request->has('exam_type_id')) {
            $query->where('exam_type_id', $request->input('exam_type_id'));
        }

        // Paginate the results
        $packages = $query->paginate($perPage);

        return ApiResponseHelper::success(
            PackageAdminResource::collection($packages),
            'Packages retrieved successfully'
        );
    }

    public function show(Package $package): JsonResponse
    {
        return ApiResponseHelper::success(
            new PackageAdminResource($package),
            'Package retrieved successfully'
        );
    }

    public function store(StorePackageRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Gather validated data from the request
            $data = $request->validated();

            // Handle image upload if provided
            if ($request->hasFile('img')) {
                Log::info('Image file found.');
                $data['img'] = $request->file('img')->store('packages', 'public');
            }

            // Create the package
            $package = Package::create($data);

            // Prepare the category data for updateOrCreate
            $categoryData = [
                'section_id' => $request->input('section_id'),
                'exam_type_id' => $request->input('exam_type_id'),
                'exam_sub_type_id' => $request->input('exam_sub_type_id'),
                'additional_package_category_id' => $request->input('additional_package_category_id')
            ];

            // Update or create the package category
            $package->packageCategory()->updateOrCreate($categoryData);

            DB::commit();

            return ApiResponseHelper::success(
                new PackageAdminResource($package),
                'Package created successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to create package', 500, $e->getMessage());
        }
    }


    public function update(UpdatePackageRequest $request, Package $package): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Gather validated data from the request
            $data = $request->validated();

            // Handle image upload if provided
            if ($request->hasFile('img')) {
                // Delete the old image if it exists
                if ($package->img && Storage::disk('public')->exists($package->img)) {
                    Storage::disk('public')->delete($package->img);
                }

                // Store the new image
                $data['img'] = $request->file('img')->store('packages', 'public');
            }

            // Update the package
            $package->update($data);

            // Prepare the category data for updateOrCreate
            $categoryData = [
                'section_id' => $request->input('section_id'),
                'exam_type_id' => $request->input('exam_type_id'),
                'exam_sub_type_id' => $request->input('exam_sub_type_id'),
                'additional_package_category_id' => $request->input('additional_package_category_id')
            ];

            // Update or create the package category
            $package->packageCategory()->updateOrCreate($categoryData);

            DB::commit();

            return ApiResponseHelper::success(
                new PackageAdminResource($package),
                'Package updated successfully'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to update package', 500, $e->getMessage());
        }
    }



    public function destroy(Package $package): JsonResponse
    {
        try {
            // Delete the package
            $package->delete();
            return ApiResponseHelper::success('Package deleted successfully');
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to delete package', 500, $e->getMessage());
        }
    }

    public function changeStatus(ChangePackageStatusRequest $request, Package $package): JsonResponse
    {
        try {
            // Update package status
            $package->update($request->all());
            return ApiResponseHelper::success(
                new PackageAdminResource($package),
                'Package status changed successfully'
            );
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to change package status', 500, $e->getMessage());
        }
    }

    public function getPackageSubscribers(Package $package, Request $request): JsonResponse
    {
        try {
            // Get per_page value from request or set default to 15
            $perPage = $request->get('per_page', 15);

            // Paginate the subscribers
            $subscribers = $package->subscribers()->paginate($perPage);

            return ApiResponseHelper::success(
                StudentResource::collection($subscribers),
                'Subscribers retrieved successfully'
            );
        } catch (\Exception $e) {
            return ApiResponseHelper::error('Failed to get subscribers', 500, $e->getMessage());
        }
    }

    public function attachPdf(AttachPdfRequest $request, Package $package): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Get the PDF ID from the request
            $pdfId = $request->input('pdf_id');
            $pdf = Pdf::findOrFail($pdfId);  // Find the PDF by ID

            // Attach the PDF to the Package using the polymorphic relationship
            $package->pdfs()->save($pdf);  // Assuming you have a 'pdfs' relationship defined on the Package model

            DB::commit();

            return ApiResponseHelper::success(null, 'PDF attached successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to attach PDF', 500, $e->getMessage());
        }
    }

    public function detachPdf(DetachPdfRequest $request, Package $package): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Get the PDF ID from the request
            $pdfId = $request->input('pdf_id');
            $pdf = Pdf::where('id', $pdfId)
                ->where('pdfable_id', $package->id)
                ->where('pdfable_type', Package::class)
                ->firstOrFail();  // Find the PDF

            // Detach the PDF from the Package
            $pdf->pdfable()->dissociate();  // Use dissociate() to break the relationship
            $pdf->save();

            DB::commit();

            return ApiResponseHelper::success(null, 'PDF detached successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to detach PDF', 500, $e->getMessage());
        }
    }


    public function attachTag(AttachPackageTagRequest $request, Package $package): JsonResponse
    {
        DB::beginTransaction();
        try {
            $tagId = $request->validated('tag_id');

            // Attach the tag to the package
            $package->tags()->attach($tagId);

            DB::commit();
            return ApiResponseHelper::success(null, 'Tag attached successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to attach tag', 500, $e->getMessage());
        }
    }

    public function detachTag(DetachPackageTagRequest $request, Package $package): JsonResponse
    {
        DB::beginTransaction();
        try {
            $tagId = $request->validated('tag_id');

            // Detach the tag from the package
            $package->tags()->detach($tagId);

            DB::commit();
            return ApiResponseHelper::success(null, 'Tag detached successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseHelper::error('Failed to detach tag', 500, $e->getMessage());
        }
    }
}
