<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add UUID columns
            $table->uuid('division_id')->nullable()->after('password');
            $table->uuid('cluster_id')->nullable()->after('division_id');

            // Add foreign keys
            $table->foreign('division_id')
                  ->references('id')->on('divisions')
                  ->nullOnDelete();

            $table->foreign('cluster_id')
                  ->references('id')->on('clusters')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['division_id']);
            $table->dropForeign(['cluster_id']);

            // Drop the columns
            $table->dropColumn(['division_id', 'cluster_id']);
        });
    }
};
