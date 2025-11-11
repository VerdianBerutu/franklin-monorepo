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
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->enum('category', ['laporan', 'sertifikat', 'bukti']);
            $table->text('description')->nullable();
            $table->string('path');
            $table->string('mime');
            $table->integer('size');
            $table->dateTime('uploaded_at');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['category', 'uploaded_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};