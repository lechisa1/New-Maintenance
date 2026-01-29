<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('work_logs', function (Blueprint $table) {
        $table->timestamp('rejected_at')->nullable()->after('log_date');
        $table->uuid('rejected_by')->nullable()->after('rejected_at');
        $table->text('rejection_reason')->nullable()->after('rejected_by');
        $table->text('rejection_notes')->nullable()->after('rejection_reason');
        
        $table->foreign('rejected_by')->references('id')->on('users')->nullOnDelete();
    });
}

public function down()
{
    Schema::table('work_logs', function (Blueprint $table) {
        $table->dropForeign(['rejected_by']);
        $table->dropColumn([
            'rejected_at',
            'rejected_by',
            'rejection_reason',
            'rejection_notes',
        ]);
    });
}
};