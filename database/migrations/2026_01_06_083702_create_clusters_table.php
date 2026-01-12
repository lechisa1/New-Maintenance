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
        Schema::create('clusters', function (Blueprint $table) {
            $table->uuid('id')->primary();
             $table->string('name')->unique();
                         // Relationship to organizations

                         $table->uuid('organization_id')->nullable();
$table->foreign('organization_id')->references('id')->on('organizations')->nullOnDelete();


            // Cluster chairman relationship
            $table->uuid('cluster_chairman')
                  ->nullable() ;
                    $table->foreign('cluster_chairman')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
         
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clusters');
    }
};
