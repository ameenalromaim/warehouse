<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'phone')) {
            return;
        }

        DB::statement('ALTER TABLE `users` MODIFY `phone` VARCHAR(32) NULL');
    }

    public function down(): void
    {
        // لا نرجع النوع السابق لأنه قد يكون غير متوافق مع البيانات الحالية
    }
};
