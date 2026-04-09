<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('maintenance_request_id')->nullable();

            $table->uuid('item_id')->nullable();
            $table->uuid('technician_id')->nullable();
            $table->uuid('issue_type_id')->nullable();
            $table->uuid('reviewed_by')->nullable();

            $table->foreign('technician_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();


            $table->foreign('issue_type_id')
                ->references('id')
                ->on('issue_types')
                ->onDelete('cascade');
            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'forwarded', 'rejected'])->default('pending');
            $table->timestamp('forwarded_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreign('reviewed_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('maintenance_request_id')
                ->references('id')
                ->on('maintenance_requests')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('approval_requests');
    }
};
