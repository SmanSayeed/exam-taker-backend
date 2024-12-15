<?php

namespace App\Http\Controllers;

use App\Models\PackageTag;
use Illuminate\Http\Request;

class PackageTagController extends Controller
{
    // Fetch all package tags
    public function index()
    {
        return response()->json(PackageTag::all(), 200);
    }

    // Create a new package tag
    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'title' => 'required|string|max:255',
            'status' => 'boolean',
            'details' => 'nullable|string',
        ]);

        $packageTag = PackageTag::create($request->all());

        return response()->json($packageTag, 201);
    }

    // Get a specific package tag by ID
    public function show($id)
    {
        $packageTag = PackageTag::find($id);

        if (!$packageTag) {
            return response()->json(['message' => 'Package Tag not found'], 404);
        }

        return response()->json($packageTag, 200);
    }

    // Update a package tag
    public function update(Request $request, $id)
    {
        $packageTag = PackageTag::find($id);

        if (!$packageTag) {
            return response()->json(['message' => 'Package Tag not found'], 404);
        }

        $request->validate([
            'package_id' => 'exists:packages,id',
            'title' => 'string|max:255',
            'status' => 'boolean',
            'details' => 'nullable|string',
        ]);

        $packageTag->update($request->all());

        return response()->json($packageTag, 200);
    }

    // Delete a package tag
    public function destroy($id)
    {
        $packageTag = PackageTag::find($id);

        if (!$packageTag) {
            return response()->json(['message' => 'Package Tag not found'], 404);
        }

        $packageTag->delete();

        return response()->json(['message' => 'Package Tag deleted successfully'], 200);
    }
}
