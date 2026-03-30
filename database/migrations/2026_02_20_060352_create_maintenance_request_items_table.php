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
        Schema::create('maintenance_request_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('maintenance_request_id');
            $table->foreign('maintenance_request_id')
                ->references('id')
                ->on('maintenance_requests')
                ->cascadeOnDelete();

            $table->uuid('item_id');
            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->cascadeOnDelete();

            $table->uuid('issue_type_id');
            $table->foreign('issue_type_id')
                ->references('id')
                ->on('issue_types')
                ->cascadeOnDelete();

            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_request_items');
    }
};
