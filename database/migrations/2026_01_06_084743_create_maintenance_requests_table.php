<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            
            $table->string('title')->nullable();
            $table->uuid('item_id')->nullable();
            $table->foreign('item_id')->references('id')->on('items')->nullOnDelete();
            
            $table->text('description');
            $table->enum('issue_type', ['hardware', 'software', 'network', 'performance', 'setup', 'upgrade', 'other'])->default('other');
            $table->enum('priority', ['low', 'medium', 'high', 'emergency'])->default('medium');
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'rejected','waiting_approval', 'not_fixed','approved','confirmed'])->default('pending');
            
            $table->string('ticket_number')->unique()->nullable();
             $table->text('approval_notes')->nullable();
            $table->uuid('assigned_to')->nullable();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            
            $table->text('technician_notes')->nullable();
            $table->text('resolution_notes')->nullable();
            
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('maintenance_request_files', function (Blueprint $table) {
            $table->id();
            $table->uuid('maintenance_request_id');
            $table->foreign('maintenance_request_id')->references('id')->on('maintenance_requests')->cascadeOnDelete();
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type');
            $table->string('path');
            $table->integer('size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_request_files');
        Schema::dropIfExists('maintenance_requests');
    }
};