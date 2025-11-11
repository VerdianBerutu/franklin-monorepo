<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Upload\StoreUploadRequest;
use App\Http\Resources\UploadResource;
use App\Models\Upload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Upload::with('user')->latest();

        // Filter berdasarkan kategori
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter berdasarkan tanggal
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('uploaded_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('uploaded_at', '<=', $request->end_date);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Authorization: Staf gudang hanya bisa lihat upload sendiri
        if ($request->user()->hasRole('staf_gudang')) {
            $query->where('user_id', $request->user()->id);
        }

        $uploads = $query->paginate(15);

        return UploadResource::collection($uploads);
    }

    public function store(StoreUploadRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $path = $file->store('uploads', 'public');

        $upload = Upload::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'category' => $request->category,
            'description' => $request->description,
            'path' => $path,
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'uploaded_at' => $request->uploaded_at ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => new UploadResource($upload),
            'message' => 'File berhasil diupload'
        ], 201);
    }

    public function show(Upload $upload): UploadResource
    {
        $this->authorize('view', $upload);
        return new UploadResource($upload->load('user'));
    }

    public function destroy(Upload $upload): JsonResponse
    {
        $this->authorize('delete', $upload);
        
        // Hapus file dari storage
        Storage::disk('public')->delete($upload->path);
        $upload->delete();

        return response()->json([
            'success' => true,
            'message' => 'File berhasil dihapus'
        ]);
    }
}