<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'type',
        'notes',
        'user_id'
    ];

    /**
     * ✅ TAMBAHAN: Boot method - Auto set user_id saat create
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-fill user_id dari user yang login
        static::creating(function ($customer) {
            if (!$customer->user_id) {
                $customer->user_id = auth()->id();
            }
        });
    }

    /**
     * Relationship: Customer belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Customer has many Sales
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}