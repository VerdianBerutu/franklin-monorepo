<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'user_id'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

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
}