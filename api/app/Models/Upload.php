<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Upload extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'category',
        'description',
        'path',
        'mime',
        'size',
        'uploaded_at'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'size' => 'integer'
    ];

    // Kategori dokumen
    const CATEGORY_LAPORAN = 'laporan';
    const CATEGORY_SERTIFIKAT = 'sertifikat';
    const CATEGORY_BUKTI = 'bukti';

    public static function getCategories()
    {
        return [
            self::CATEGORY_LAPORAN => 'Laporan',
            self::CATEGORY_SERTIFIKAT => 'Sertifikat',
            self::CATEGORY_BUKTI => 'Bukti',
        ];
    }

    // Relasi dengan user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor untuk URL file
    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }
}