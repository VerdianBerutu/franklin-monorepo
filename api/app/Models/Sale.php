<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'sale_date',
        'customer_id',
        'customer_name',
        'subtotal',
        'tax',
        'discount',
        'total',
        'payment_status',
        'payment_method',
        'payment_date',
        'notes',
        'shipping_address',
        'user_id'
    ];

    // ✅ CRITICAL FIX: Jangan cast ke decimal, pakai float
    protected $casts = [
        'sale_date' => 'datetime',
        'payment_date' => 'datetime',
    ];

    protected $appends = ['formatted_total', 'status_label'];

    /**
     * Relationships
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Accessors
     */
    public function getFormattedTotalAttribute()
    {
        $total = $this->attributes['total'] ?? 0;
        return 'Rp ' . number_format($total, 0, ',', '.');
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'partial' => 'Partial',
            'cancelled' => 'Cancelled'
        ];
        return $labels[$this->payment_status] ?? $this->payment_status;
    }

    /**
     * Scopes
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('sale_date', now()->month)
                     ->whereYear('sale_date', now()->year);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('items.product', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        return $query;
    }

    public function scopeStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('payment_status', $status);
        }
        return $query;
    }

    public function scopeDateRange($query, $dateRange)
    {
        if ($dateRange) {
            $dates = explode(' to ', $dateRange);
            if (count($dates) === 2) {
                $startDate = $dates[0];
                $endDate = $dates[1];
                return $query->whereBetween('sale_date', [$startDate, $endDate]);
            } elseif (count($dates) === 1) {
                return $query->whereDate('sale_date', $dates[0]);
            }
        }
        return $query;
    }

    public function scopeCustomer($query, $customerId)
    {
        if ($customerId) {
            return $query->where('customer_id', $customerId);
        }
        return $query;
    }

    public function scopeDate($query, $date)
    {
        if ($date) {
            return $query->whereDate('sale_date', $date);
        }
        return $query;
    }

    /**
     * Auto-generate invoice number
     */
    public static function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $year = now()->year;
        $month = now()->format('m');
        
        $lastSale = self::whereYear('created_at', $year)
                        ->whereMonth('created_at', now()->month)
                        ->orderBy('id', 'desc')
                        ->first();
        
        $number = $lastSale ? (int)substr($lastSale->invoice_number, -4) + 1 : 1;
        
        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $number);
    }

    /**
     * Calculate totals from items
     */
    public function calculateTotals()
    {
        $subtotal = $this->items->sum('subtotal');
        
        $this->subtotal = $subtotal;
        $this->total = $subtotal + $this->tax - $this->discount;
        $this->save();
    }

    /**
     * Update sale status based on payment
     */
    public function updatePaymentStatus()
    {
        if (method_exists($this, 'payments')) {
            $totalPaid = $this->payments()->sum('amount');
            
            if ($totalPaid >= $this->total) {
                $this->payment_status = 'paid';
            } elseif ($totalPaid > 0) {
                $this->payment_status = 'partial';
            } else {
                $this->payment_status = 'pending';
            }
            
            $this->save();
        }
    }

    /**
     * Check if sale can be deleted
     */
    public function canBeDeleted()
    {
        return $this->payment_status !== 'paid';
    }

    /**
     * Get sales summary for dashboard
     */
    public static function getDashboardSummary()
    {
        return [
            'today_sales' => self::whereDate('sale_date', today())->paid()->sum('total'),
            'month_sales' => self::thisMonth()->paid()->sum('total'),
            'total_sales' => self::paid()->sum('total'),
            'pending_orders' => self::pending()->count(),
        ];
    }

    /**
     * Boot method - SIMPLIFIED, NO OVERRIDE
     */
    protected static function boot()
    {
        parent::boot();

        // ✅ HANYA set invoice_number dan sale_date jika kosong
        static::creating(function ($sale) {
            if (!$sale->invoice_number) {
                $sale->invoice_number = self::generateInvoiceNumber();
            }
            if (!$sale->sale_date) {
                $sale->sale_date = now();
            }
            // ✅ JANGAN TOUCH TOTAL - BIARKAN CONTROLLER YANG SET
        });

        static::updating(function ($sale) {
            // Auto-update payment date jika status berubah menjadi paid
            if ($sale->isDirty('payment_status') && $sale->payment_status === 'paid' && !$sale->payment_date) {
                $sale->payment_date = now();
            }
        });

        static::deleting(function ($sale) {
            // Hapus related items
            $sale->items()->delete();
            
            // Hapus related payments jika ada
            if (method_exists($sale, 'payments')) {
                $sale->payments()->delete();
            }
        });
    }
}