<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Columns for offline-first / multi-device sync:
     * - deleted_at: soft deletes (tombstones for sync)
     * - synced_at: last successful replication timestamp (nullable until synced)
     * - version: optimistic concurrency / conflict detection
     * - updated_by_device: client device identifier (UUID string)
     */
    private function tables(): array
    {
        return [
            'users',
            'units',
            'suppliers',
            'product',
            'purchase',
            'purchaseitem',
            'customers',
            'returns',
            'return_items',
        ];
    }

    public function up(): void
    {
        foreach ($this->tables() as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                if (! Schema::hasColumn($table, 'deleted_at')) {
                    $blueprint->softDeletes();
                }
                if (! Schema::hasColumn($table, 'synced_at')) {
                    $blueprint->timestamp('synced_at')->nullable();
                }
                if (! Schema::hasColumn($table, 'version')) {
                    $blueprint->unsignedInteger('version')->default(1);
                }
                if (! Schema::hasColumn($table, 'updated_by_device')) {
                    $blueprint->string('updated_by_device', 36)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables() as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                if (Schema::hasColumn($table, 'updated_by_device')) {
                    $blueprint->dropColumn('updated_by_device');
                }
                if (Schema::hasColumn($table, 'version')) {
                    $blueprint->dropColumn('version');
                }
                if (Schema::hasColumn($table, 'synced_at')) {
                    $blueprint->dropColumn('synced_at');
                }
                if (Schema::hasColumn($table, 'deleted_at')) {
                    $blueprint->dropSoftDeletes();
                }
            });
        }
    }
};
