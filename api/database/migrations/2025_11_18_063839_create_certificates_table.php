<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('certificate_number')->unique();
            $table->text('description')->nullable();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->string('issuing_authority');
            $table->string('status')->default('active'); // active, expired, revoked
            $table->string('file_path')->nullable(); // untuk upload file sertifikat
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};