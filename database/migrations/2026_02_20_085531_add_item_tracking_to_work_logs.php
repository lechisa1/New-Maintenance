<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('work_logs', function (Blueprint $table) {
            // Add JSON column to store which items were worked on
            $table->json('item_ids')->nullable()->after('request_id');
            // Add JSON column to store issue types addressed
            $table->json('issue_type_ids')->nullable()->after('item_ids');
            // Add notes per item
            $table->json('item_notes')->nullable()->after('completion_notes');
        });
    }

    public function down()
    {
        Schema::table('work_logs', function (Blueprint $table) {
            $table->dropColumn(['item_ids', 'issue_type_ids', 'item_notes']);
        });
    }
};
