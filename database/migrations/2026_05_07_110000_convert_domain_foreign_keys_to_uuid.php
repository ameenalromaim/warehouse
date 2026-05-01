<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->convertProductUnitFk();
        $this->convertPurchaseSupplierFk();
        $this->convertPurchaseItemsFk();
        $this->convertReturnsSupplierFk();
        $this->convertReturnItemsFk();
    }

    public function down(): void
    {
        $this->restoreReturnItemsFk();
        $this->restoreReturnsSupplierFk();
        $this->restorePurchaseItemsFk();
        $this->restorePurchaseSupplierFk();
        $this->restoreProductUnitFk();
    }

    private function convertProductUnitFk(): void
    {
        if (! Schema::hasTable('product') || ! Schema::hasColumn('product', 'unit_id')) {
            return;
        }

        Schema::table('product', function (Blueprint $blueprint) {
            $blueprint->uuid('unit_uuid')->nullable();
        });

        DB::statement('UPDATE product INNER JOIN units ON product.unit_id = units.id SET product.unit_uuid = units.uuid');

        Schema::table('product', function (Blueprint $blueprint) {
            $blueprint->dropForeign(['unit_id']);
            $blueprint->dropColumn('unit_id');
        });

        Schema::table('product', function (Blueprint $blueprint) {
            $blueprint->uuid('unit_uuid')->nullable(false)->change();
            $blueprint->foreign('unit_uuid')->references('uuid')->on('units')->cascadeOnDelete();
        });
    }

    private function convertPurchaseSupplierFk(): void
    {
        if (! Schema::hasTable('purchase') || ! Schema::hasColumn('purchase', 'supplier_id')) {
            return;
        }

        Schema::table('purchase', function (Blueprint $blueprint) {
            $blueprint->uuid('supplier_uuid')->nullable();
        });

        DB::statement('UPDATE purchase INNER JOIN suppliers ON purchase.supplier_id = suppliers.id SET purchase.supplier_uuid = suppliers.uuid');

        Schema::table('purchase', function (Blueprint $blueprint) {
            $blueprint->dropForeign(['supplier_id']);
            $blueprint->dropColumn('supplier_id');
        });

        Schema::table('purchase', function (Blueprint $blueprint) {
            $blueprint->uuid('supplier_uuid')->nullable(false)->change();
            $blueprint->foreign('supplier_uuid')->references('uuid')->on('suppliers')->cascadeOnDelete();
        });
    }

    private function convertPurchaseItemsFk(): void
    {
        if (! Schema::hasTable('purchaseitem') || ! Schema::hasColumn('purchaseitem', 'purchase_id')) {
            return;
        }

        Schema::table('purchaseitem', function (Blueprint $blueprint) {
            $blueprint->uuid('purchase_uuid')->nullable();
            $blueprint->uuid('product_uuid')->nullable();
            $blueprint->uuid('unit_uuid')->nullable();
        });

        DB::statement('UPDATE purchaseitem INNER JOIN purchase ON purchaseitem.purchase_id = purchase.id SET purchaseitem.purchase_uuid = purchase.uuid');
        DB::statement('UPDATE purchaseitem INNER JOIN product ON purchaseitem.product_id = product.id SET purchaseitem.product_uuid = product.uuid');
        DB::statement('UPDATE purchaseitem INNER JOIN units ON purchaseitem.unit_id = units.id SET purchaseitem.unit_uuid = units.uuid');

        Schema::table('purchaseitem', function (Blueprint $blueprint) {
            $blueprint->dropForeign(['purchase_id']);
            $blueprint->dropForeign(['product_id']);
            $blueprint->dropForeign(['unit_id']);
            $blueprint->dropColumn(['purchase_id', 'product_id', 'unit_id']);
        });

        Schema::table('purchaseitem', function (Blueprint $blueprint) {
            $blueprint->uuid('purchase_uuid')->nullable(false)->change();
            $blueprint->uuid('product_uuid')->nullable(false)->change();
            $blueprint->uuid('unit_uuid')->nullable(false)->change();
            $blueprint->foreign('purchase_uuid')->references('uuid')->on('purchase')->cascadeOnDelete();
            $blueprint->foreign('product_uuid')->references('uuid')->on('product')->cascadeOnDelete();
            $blueprint->foreign('unit_uuid')->references('uuid')->on('units')->cascadeOnDelete();
        });
    }

    private function convertReturnsSupplierFk(): void
    {
        if (! Schema::hasTable('returns') || ! Schema::hasColumn('returns', 'supplier_id')) {
            return;
        }

        Schema::table('returns', function (Blueprint $blueprint) {
            $blueprint->uuid('supplier_uuid')->nullable();
        });

        DB::statement('UPDATE returns INNER JOIN suppliers ON returns.supplier_id = suppliers.id SET returns.supplier_uuid = suppliers.uuid WHERE returns.supplier_id IS NOT NULL');

        Schema::table('returns', function (Blueprint $blueprint) {
            $blueprint->dropConstrainedForeignId('supplier_id');
        });

        Schema::table('returns', function (Blueprint $blueprint) {
            $blueprint->foreign('supplier_uuid')->references('uuid')->on('suppliers')->nullOnDelete();
        });
    }

    private function convertReturnItemsFk(): void
    {
        if (! Schema::hasTable('return_items') || ! Schema::hasColumn('return_items', 'return_id')) {
            return;
        }

        Schema::table('return_items', function (Blueprint $blueprint) {
            $blueprint->uuid('return_uuid')->nullable();
            $blueprint->uuid('product_uuid')->nullable();
            $blueprint->uuid('unit_uuid')->nullable();
        });

        DB::statement('UPDATE return_items INNER JOIN returns ON return_items.return_id = returns.id SET return_items.return_uuid = returns.uuid');
        DB::statement('UPDATE return_items INNER JOIN product ON return_items.product_id = product.id SET return_items.product_uuid = product.uuid');
        DB::statement('UPDATE return_items INNER JOIN units ON return_items.unit_id = units.id SET return_items.unit_uuid = units.uuid');

        Schema::table('return_items', function (Blueprint $blueprint) {
            $blueprint->dropForeign(['return_id']);
            $blueprint->dropForeign(['product_id']);
            $blueprint->dropForeign(['unit_id']);
            $blueprint->dropColumn(['return_id', 'product_id', 'unit_id']);
        });

        Schema::table('return_items', function (Blueprint $blueprint) {
            $blueprint->uuid('return_uuid')->nullable(false)->change();
            $blueprint->uuid('product_uuid')->nullable(false)->change();
            $blueprint->uuid('unit_uuid')->nullable(false)->change();
            $blueprint->foreign('return_uuid')->references('uuid')->on('returns')->cascadeOnDelete();
            $blueprint->foreign('product_uuid')->references('uuid')->on('product')->cascadeOnDelete();
            $blueprint->foreign('unit_uuid')->references('uuid')->on('units')->cascadeOnDelete();
        });
    }

    private function restoreProductUnitFk(): void
    {
        if (! Schema::hasTable('product') || ! Schema::hasColumn('product', 'unit_uuid')) {
            return;
        }

        Schema::table('product', function (Blueprint $blueprint) {
            $blueprint->dropForeign(['unit_uuid']);
        });

        Schema::table('product', function (Blueprint $blueprint) {
            $blueprint->unsignedBigInteger('unit_id')->nullable();
        });

        DB::statement('UPDATE product INNER JOIN units ON product.unit_uuid = units.uuid SET product.unit_id = units.id');

        Schema::table('product', function (Blueprint $blueprint) {
            $blueprint->dropColumn('unit_uuid');
        });

        Schema::table('product', function (Blueprint $blueprint) {
            $blueprint->unsignedBigInteger('unit_id')->nullable(false)->change();
            $blueprint->foreign('unit_id')->references('id')->on('units')->cascadeOnDelete();
        });
    }

    private function restorePurchaseSupplierFk(): void
    {
        if (! Schema::hasTable('purchase') || ! Schema::hasColumn('purchase', 'supplier_uuid')) {
            return;
        }

        Schema::table('purchase', function (Blueprint $blueprint) {
            $blueprint->dropForeign(['supplier_uuid']);
        });

        Schema::table('purchase', function (Blueprint $blueprint) {
            $blueprint->unsignedBigInteger('supplier_id')->nullable();
        });

        DB::statement('UPDATE purchase INNER JOIN suppliers ON purchase.supplier_uuid = suppliers.uuid SET purchase.supplier_id = suppliers.id');

        Schema::table('purchase', function (Blueprint $blueprint) {
            $blueprint->dropColumn('supplier_uuid');
        });

        Schema::table('purchase', function (Blueprint $blueprint) {
            $blueprint->unsignedBigInteger('supplier_id')->nullable(false)->change();
            $blueprint->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete();
        });
    }

    private function restorePurchaseItemsFk(): void
    {
        if (! Schema::hasTable('purchaseitem') || ! Schema::hasColumn('purchaseitem', 'purchase_uuid')) {
            return;
        }

        Schema::table('purchaseitem', function (Blueprint $blueprint) {
            $blueprint->dropForeign(['purchase_uuid']);
            $blueprint->dropForeign(['product_uuid']);
            $blueprint->dropForeign(['unit_uuid']);
        });

        Schema::table('purchaseitem', function (Blueprint $blueprint) {
            $blueprint->unsignedBigInteger('purchase_id')->nullable();
            $blueprint->unsignedBigInteger('product_id')->nullable();
            $blueprint->unsignedBigInteger('unit_id')->nullable();
        });

        DB::statement('UPDATE purchaseitem INNER JOIN purchase ON purchaseitem.purchase_uuid = purchase.uuid SET purchaseitem.purchase_id = purchase.id');
        DB::statement('UPDATE purchaseitem INNER JOIN product ON purchaseitem.product_uuid = product.uuid SET purchaseitem.product_id = product.id');
        DB::statement('UPDATE purchaseitem INNER JOIN units ON purchaseitem.unit_uuid = units.uuid SET purchaseitem.unit_id = units.id');

        Schema::table('purchaseitem', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['purchase_uuid', 'product_uuid', 'unit_uuid']);
            $blueprint->unsignedBigInteger('purchase_id')->nullable(false)->change();
            $blueprint->unsignedBigInteger('product_id')->nullable(false)->change();
            $blueprint->unsignedBigInteger('unit_id')->nullable(false)->change();
            $blueprint->foreign('purchase_id')->references('id')->on('purchase')->cascadeOnDelete();
            $blueprint->foreign('product_id')->references('id')->on('product')->cascadeOnDelete();
            $blueprint->foreign('unit_id')->references('id')->on('units')->cascadeOnDelete();
        });
    }

    private function restoreReturnsSupplierFk(): void
    {
        if (! Schema::hasTable('returns') || ! Schema::hasColumn('returns', 'supplier_uuid')) {
            return;
        }

        Schema::table('returns', function (Blueprint $blueprint) {
            $blueprint->dropForeign(['supplier_uuid']);
        });

        Schema::table('returns', function (Blueprint $blueprint) {
            $blueprint->unsignedBigInteger('supplier_id')->nullable();
        });

        DB::statement('UPDATE returns INNER JOIN suppliers ON returns.supplier_uuid = suppliers.uuid SET returns.supplier_id = suppliers.id WHERE returns.supplier_uuid IS NOT NULL');

        Schema::table('returns', function (Blueprint $blueprint) {
            $blueprint->dropColumn('supplier_uuid');
        });

        Schema::table('returns', function (Blueprint $blueprint) {
            $blueprint->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
        });
    }

    private function restoreReturnItemsFk(): void
    {
        if (! Schema::hasTable('return_items') || ! Schema::hasColumn('return_items', 'return_uuid')) {
            return;
        }

        Schema::table('return_items', function (Blueprint $blueprint) {
            $blueprint->dropForeign(['return_uuid']);
            $blueprint->dropForeign(['product_uuid']);
            $blueprint->dropForeign(['unit_uuid']);
        });

        Schema::table('return_items', function (Blueprint $blueprint) {
            $blueprint->unsignedBigInteger('return_id')->nullable();
            $blueprint->unsignedBigInteger('product_id')->nullable();
            $blueprint->unsignedBigInteger('unit_id')->nullable();
        });

        DB::statement('UPDATE return_items INNER JOIN returns ON return_items.return_uuid = returns.uuid SET return_items.return_id = returns.id');
        DB::statement('UPDATE return_items INNER JOIN product ON return_items.product_uuid = product.uuid SET return_items.product_id = product.id');
        DB::statement('UPDATE return_items INNER JOIN units ON return_items.unit_uuid = units.uuid SET return_items.unit_id = units.id');

        Schema::table('return_items', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['return_uuid', 'product_uuid', 'unit_uuid']);
            $blueprint->unsignedBigInteger('return_id')->nullable(false)->change();
            $blueprint->unsignedBigInteger('product_id')->nullable(false)->change();
            $blueprint->unsignedBigInteger('unit_id')->nullable(false)->change();
            $blueprint->foreign('return_id')->references('id')->on('returns')->cascadeOnDelete();
            $blueprint->foreign('product_id')->references('id')->on('product')->cascadeOnDelete();
            $blueprint->foreign('unit_id')->references('id')->on('units')->cascadeOnDelete();
        });
    }
};
