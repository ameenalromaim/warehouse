<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fixes SQLSTATE[1265] Data truncated for column `type_location` when the DB column
     * was created as ENUM or a short VARCHAR (e.g. outside this repo). Ensures VARCHAR(255).
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'type_location')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('type_location', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Intentionally left blank: previous column definition is unknown.
    }
};
