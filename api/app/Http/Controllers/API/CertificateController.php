<?php

namespace App\Http\Controllers\API;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function index(): JsonResponse
    {
        $certificates = Certificate::with('user:id,name')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $certificates
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'certificate_number' => 'required|string|unique:certificates',
            'description' => 'nullable|string',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'issuing_authority' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('certificates', 'public');
            $validated['file_path'] = $path;
        }

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'active';

        $certificate = Certificate::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Certificate created successfully',
            'data' => $certificate->load('user:id,name')
        ], 201);
    }

    public function show(Certificate $certificate): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $certificate->load('user:id,name')
        ]);
    }

    public function update(Request $request, Certificate $certificate): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'certificate_number' => 'sometimes|required|string|unique:certificates,certificate_number,' . $certificate->id,
            'description' => 'nullable|string',
            'issue_date' => 'sometimes|required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'issuing_authority' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|in:active,expired,revoked',
            'file' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($certificate->file_path) {
                Storage::disk('public')->delete($certificate->file_path);
            }
            
            $path = $request->file('file')->store('certificates', 'public');
            $validated['file_path'] = $path;
        }

        $certificate->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Certificate updated successfully',
            'data' => $certificate->load('user:id,name')
        ]);
    }

    public function destroy(Certificate $certificate): JsonResponse
    {
        // Delete file if exists
        if ($certificate->file_path) {
            Storage::disk('public')->delete($certificate->file_path);
        }

        $certificate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Certificate deleted successfully'
        ]);
    }
}