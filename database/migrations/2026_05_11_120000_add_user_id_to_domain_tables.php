<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * من أضاف السجل (فاتورة، منتج، مردود، مورد، وحدة، بنود…).
     * يُملأ من المستخدم المصادَق عليه عند الإنشاء أو المزامنة.
     */
    private function tables(): array
    {
        return [
            'product',
            'purchase',
            'purchaseitem',
            'returns',
            'return_items',
            'suppliers',
            'units',
        ];
    }

    public function up(): void
    {
        foreach ($this->tables() as $table) {
            if (! Schema::hasTable($table) || Schema::hasColumn($table, 'user_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreignId('user_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables() as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'user_id')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropConstrainedForeignId('user_id');
            });
        }
    }
};
