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
Schema::table('maintenance_requests', function (Blueprint $table) {
    $table->uuid('issue_type_id')->nullable()->after('description');

    $table->foreign('issue_type_id')
          ->references('id')
          ->on('issue_types')
          ->nullOnDelete();

    $table->dropColumn('issue_type');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            //
        });
    }
};
