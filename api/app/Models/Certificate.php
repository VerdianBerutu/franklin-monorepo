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
        'file_path',
        'file_name',      // ✅ TAMBAHKAN
        'file_size',      // ✅ TAMBAHKAN
        'mime_type',      // ✅ TAMBAHKAN
        'user_id'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

   //rotected $appends = ['download_url']; // ✅ TAMBAHKAN

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

    // ✅ TAMBAHKAN: Get download URL
    public function getDownloadUrlAttribute()
    {
        return $this->file_path ? route('certificates.download', $this->id) : null;
    }

    // ✅ TAMBAHKAN: Auto delete file when certificate deleted
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