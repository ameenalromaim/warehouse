<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'type_location')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('type_location', 255)->nullable()->after('name');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'type_location')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('type_location');
        });
    }
};
