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
        Schema::create('assignments', function (Blueprint $table) {
     $table->uuid('id')->primary();
    $table->uuid('request_id')->nullable();
    $table->foreign('request_id')->references('id')->on('maintenance_requests')->nullOnDelete();
    
    $table->uuid('director_id')->nullable();
    $table->foreign('director_id')->references('id')->on('users')->nullOnDelete();
    $table->uuid('technician_id')->nullable();
    $table->foreign('technician_id')->references('id')->on('users')->nullOnDelete();
    $table->text('director_notes')->nullable();
    $table->timestamp('assigned_at')->useCurrent();
    $table->timestamp('expected_completion_date')->nullable();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};