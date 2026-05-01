<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Domain tables: uuid is required; existing rows are backfilled.
     */
    private function strictTables(): array
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
            'personal_access_tokens',
        ];
    }

    /**
     * Framework-managed tables: uuid stays nullable so Laravel can insert rows without knowing uuid.
     * Keys are table => primary key column for backfill iteration.
     *
     * `failed_jobs` already defines uuid in the default migration — skipped here.
     */
    private function frameworkTables(): array
    {
        return [
            'password_reset_tokens' => 'email',
            'sessions' => 'id',
            'cache' => 'key',
            'cache_locks' => 'key',
            'jobs' => 'id',
            'job_batches' => 'id',
        ];
    }

    public function up(): void
    {
        foreach ($this->strictTables() as $table) {
            $this->addStrictUuid($table);
        }

        foreach ($this->frameworkTables() as $table => $pk) {
            $this->addFrameworkUuid($table, $pk);
        }
    }

    public function down(): void
    {
        foreach (array_merge($this->strictTables(), array_keys($this->frameworkTables())) as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'uuid')) {
                continue;
            }

            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }

    private function addStrictUuid(string $table): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        if (! Schema::hasColumn($table, 'uuid')) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->uuid('uuid')->nullable();
            });
        }

        $this->backfillNumericIdTable($table);

        Schema::table($table, function (Blueprint $blueprint) {
            $blueprint->uuid('uuid')->nullable(false)->change();
        });

        if (! $this->uuidColumnHasUniqueIndex($table)) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->unique('uuid');
            });
        }
    }

    private function addFrameworkUuid(string $table, string $pk): void
    {
        if (! Schema::hasTable($table) || Schema::hasColumn($table, 'uuid')) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) {
            $blueprint->uuid('uuid')->nullable()->unique();
        });

        $this->backfillFrameworkTable($table, $pk);
    }

    private function uuidColumnHasUniqueIndex(string $table): bool
    {
        $indexes = DB::select(
            'SHOW INDEX FROM `'.str_replace('`', '``', $table).'` WHERE Column_name = ? AND Non_unique = ?',
            ['uuid', 0]
        );

        return count($indexes) > 0;
    }

    private function backfillNumericIdTable(string $table): void
    {
        DB::table($table)->whereNull('uuid')->orderBy('id')->chunkById(500, function ($rows) use ($table) {
            foreach ($rows as $row) {
                DB::table($table)->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
            }
        });
    }

    private function backfillFrameworkTable(string $table, string $pk): void
    {
        match ($pk) {
            'email' => DB::table($table)->whereNull('uuid')->orderBy('email')->chunk(200, function ($rows) use ($table) {
                foreach ($rows as $row) {
                    DB::table($table)->where('email', $row->email)->update(['uuid' => (string) Str::uuid()]);
                }
            }),
            'key' => DB::table($table)->whereNull('uuid')->orderBy('key')->chunk(200, function ($rows) use ($table) {
                foreach ($rows as $row) {
                    DB::table($table)->where('key', $row->key)->update(['uuid' => (string) Str::uuid()]);
                }
            }),
            'id' => $this->backfillFrameworkById($table),
            default => null,
        };
    }

    private function backfillFrameworkById(string $table): void
    {
        if ($table === 'sessions' || $table === 'job_batches') {
            DB::table($table)->whereNull('uuid')->orderBy('id')->chunk(200, function ($rows) use ($table) {
                foreach ($rows as $row) {
                    DB::table($table)->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
                }
            });

            return;
        }

        DB::table($table)->whereNull('uuid')->orderBy('id')->chunkById(500, function ($rows) use ($table) {
            foreach ($rows as $row) {
                DB::table($table)->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
            }
        });
    }
};
