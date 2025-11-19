<?php

namespace App\Http\Controllers\API;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('certificates', $filename, 'public');
            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
            $validated['mime_type'] = $file->getMimeType();
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
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
                Storage::disk('public')->delete($certificate->file_path);
            }
            
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('certificates', $filename, 'public');
            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
            $validated['mime_type'] = $file->getMimeType();
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
        if ($certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
            Storage::disk('public')->delete($certificate->file_path);
        }

        $certificate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Certificate deleted successfully'
        ]);
    }

    /**
     * Download certificate file
     */
    public function download(Certificate $certificate): BinaryFileResponse|JsonResponse
    {
        try {
            if (!$certificate->file_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate file not found'
                ], 404);
            }

            $filePath = storage_path('app/public/' . $certificate->file_path);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found on server'
                ], 404);
            }

            // Get original filename or use certificate number
            $filename = $certificate->file_name ?? $certificate->certificate_number . '.' . pathinfo($filePath, PATHINFO_EXTENSION);

            return response()->download($filePath, $filename, [
                'Content-Type' => $certificate->mime_type ?? 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View certificate file in browser
     */
    public function view(Certificate $certificate): BinaryFileResponse|JsonResponse
    {
        try {
            if (!$certificate->file_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate file not found'
                ], 404);
            }

            $filePath = storage_path('app/public/' . $certificate->file_path);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found on server'
                ], 404);
            }

            return response()->file($filePath, [
                'Content-Type' => $certificate->mime_type ?? 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . ($certificate->file_name ?? 'certificate') . '"',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to view file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get certificate statistics
     */
    public function statistics(): JsonResponse
    {
        $total = Certificate::count();
        $active = Certificate::where('status', 'active')->count();
        $expired = Certificate::where('status', 'expired')->count();
        $expiringSoon = Certificate::where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>', now())
            ->where('status', 'active')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'expired' => $expired,
                'expiring_soon' => $expiringSoon,
            ]
        ]);
    }
}