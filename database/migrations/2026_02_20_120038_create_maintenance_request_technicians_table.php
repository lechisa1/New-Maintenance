<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenance_request_technicians', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('maintenance_request_id');
            $table->foreign('maintenance_request_id')
                ->references('id')
                ->on('maintenance_requests')
                ->cascadeOnDelete();
            $table->uuid('user_id');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->json('item_ids')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['assigned', 'in_progress', 'completed', 'cancelled'])->default('assigned');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // $table->foreign('maintenance_request_id')
            //     ->references('id')
            //     ->on('maintenance_requests')
            //     ->onDelete('cascade');


        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_request_technicians');
    }
};
