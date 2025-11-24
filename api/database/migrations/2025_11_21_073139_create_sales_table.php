<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->date('sale_date');
            
            // Customer relation
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->string('customer_name')->nullable(); // Backup if customer deleted
            
            // Amounts
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            
            // Payment
            $table->enum('payment_status', ['pending', 'paid', 'partial', 'cancelled'])->default('pending');
            $table->string('payment_method')->nullable(); // bank_transfer, cash, credit_card, etc
            $table->date('payment_date')->nullable();
            
            // Additional info
            $table->text('notes')->nullable();
            $table->string('shipping_address')->nullable();
            
            // User tracking
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes(); // For soft delete

            // Indexes
            $table->index('invoice_number');
            $table->index('sale_date');
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};