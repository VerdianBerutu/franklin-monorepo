<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'certificate_number', 
        'description',
        'issue_date',
        'expiry_date',
        'issuing_authority',
        'status',
        'file_path',    // ✅ HANYA YANG ADA DI DATABASE
        'user_id'       // ✅ HANYA YANG ADA DI DATABASE
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    // ✅ CORRECT: Hapus appends untuk download_url jika tidak ada attribute-nya
    // protected $appends = ['download_url'];

    // Relasi dengan user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk status aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Cek apakah sertifikat expired
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    // ✅ CORRECT: Get download URL (accessor)
    public function getDownloadUrlAttribute()
    {
        return $this->file_path ? route('certificates.download', $this->id) : null;
    }

    // ✅ Accessor untuk file_name dari file_path
    public function getFileNameAttribute()
    {
        return $this->file_path ? basename($this->file_path) : null;
    }

    // ✅ Accessor untuk file_size (jika perlu, dari storage)
    public function getFileSizeAttribute()
    {
        if (!$this->file_path) return null;
        
        try {
            return Storage::disk('public')->size($this->file_path);
        } catch (\Exception $e) {
            return null;
        }
    }

    // ✅ Accessor untuk mime_type (jika perlu, dari storage)
    public function getMimeTypeAttribute()
    {
        if (!$this->file_path) return null;
        
        try {
            return Storage::disk('public')->mimeType($this->file_path);
        } catch (\Exception $e) {
            return null;
        }
    }

    // ✅ Auto delete file when certificate deleted
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($certificate) {
            if ($certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
                Storage::disk('public')->delete($certificate->file_path);
            }
        });
    }
}