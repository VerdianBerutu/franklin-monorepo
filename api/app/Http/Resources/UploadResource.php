<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UploadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'category' => $this->category,
            'category_label' => $this->getCategoryLabel(),
            'description' => $this->description,
            'file_url' => $this->file_url,
            'mime' => $this->mime,
            'size' => $this->size,
            'size_formatted' => $this->getFormattedSize(),
            'uploaded_at' => $this->uploaded_at?->format('d/m/Y'),
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name
                ];
            })
        ];
    }

    private function getCategoryLabel(): string
    {
        return Upload::getCategories()[$this->category] ?? $this->category;
    }

    private function getFormattedSize(): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }
}