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
    $table->boolean('is_approved')->default(false)->after('status');
    $table->uuid('approved_by')->nullable()->after('is_approved');
    $table->timestamp('approved_at')->nullable()->after('approved_by');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            //
            $table->dropColumn('is_approved');
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_at');
        });
    }
};
