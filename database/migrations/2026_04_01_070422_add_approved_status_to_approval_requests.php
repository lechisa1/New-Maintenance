<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // For MySQL 8.0+ you can modify enum directly
        DB::statement("ALTER TABLE approval_requests MODIFY COLUMN status ENUM('pending', 'forwarded', 'rejected', 'approved') DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE approval_requests MODIFY COLUMN status ENUM('pending', 'forwarded', 'rejected') DEFAULT 'pending'");
    }
};
