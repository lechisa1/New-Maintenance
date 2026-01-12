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
        Schema::create('divisions', function (Blueprint $table) {
           $table->uuid('id')->primary();
                         $table->string('name')->unique();
                         // Relationship to clusters
                            $table->uuid('cluster_id')->nullable();
            $table->foreign('cluster_id')->references('id')->on('clusters')->nullOnDelete();
            // Division chairman relationship
            $table->uuid('division_chairman')->nullable();
            $table->foreign('division_chairman')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};
