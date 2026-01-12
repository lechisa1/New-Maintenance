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
        Schema::create('request_updates', function (Blueprint $table) {
      $table->uuid('id')->primary();
    $table->uuid('request_id')->nullable();
    $table->foreign('request_id')->references('id')->on('maintenance_requests')->nullOnDelete();
    $table->uuid('user_id')->nullable();
    $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
    $table->text('update_text');
    $table->string('update_type'); // status_change, note, etc.
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_updates');
    }
};