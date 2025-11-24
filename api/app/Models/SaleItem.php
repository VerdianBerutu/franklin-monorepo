<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_name',
        'product_sku',
        'quantity',
        'price',
        'subtotal'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Boot method - FIXED VERSION
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            // Calculate subtotal
            $item->subtotal = $item->quantity * $item->price;
            
            // 🔥 FIX: Only auto-fill product details if product_name is truly empty
            // Don't overwrite product_name from form input under any circumstances
            if ($item->product_id && (empty($item->product_name) || $item->product_name === '' || $item->product_name === null)) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $item->product_name = $product->name;
                    $item->product_sku = $product->sku ?? null;
                }
            }
            
            // 🔥 FIX: Price should always come from form input
            // Never overwrite price from database
        });

        static::updating(function ($item) {
            // Recalculate subtotal
            $item->subtotal = $item->quantity * $item->price;
        });
    }
}