<?php

namespace App\Http\Support;

use App\Models\product;
use App\Models\Purchase;
use App\Models\purchaseitem;
use App\Models\ReturnItem;
use App\Models\ReturnModel;
use App\Models\suppliers;
use App\Models\units;
use App\Models\User;

final class ApiPresenter
{
    /**
     * ملخص المستخدم لعرضه مع السجلات (عند تحميل علاقة creator).
     */
    public static function creatorBrief(?User $u): ?array
    {
        if ($u === null) {
            return null;
        }

        return [
            'id' => $u->id,
            'uuid' => $u->uuid,
            'name' => $u->name,
        ];
    }

    public static function userPublic(User $u): array
    {
        return [
            'uuid' => $u->uuid,
            'name' => $u->name,
            'email' => $u->email,
            'phone' => $u->phone,
            'role' => $u->role,
            'type_location' => $u->type_location,
        ];
    }

    public static function unit(units $u): array
    {
        $out = [
            'uuid' => $u->uuid,
            'name' => $u->name,
            'user_id' => $u->user_id,
        ];
        if ($u->relationLoaded('creator')) {
            $out['creator'] = self::creatorBrief($u->creator);
        }

        return $out;
    }

    public static function product(product $p): array
    {
        $p->loadMissing('unit');

        $out = [
            'uuid' => $p->uuid,
            'name' => $p->name,
            'code' => $p->code,
            'description' => $p->description,
            'unit_uuid' => $p->unit_uuid,
            'type_location' => $p->type_location,
            'user_id' => $p->user_id,
            'unit' => $p->unit ? self::unit($p->unit) : null,
            'created_at' => $p->created_at?->toIso8601String(),
            'updated_at' => $p->updated_at?->toIso8601String(),
        ];
        if ($p->relationLoaded('creator')) {
            $out['creator'] = self::creatorBrief($p->creator);
        }

        return $out;
    }

    public static function supplier(suppliers $s): array
    {
        $out = [
            'uuid' => $s->uuid,
            'name' => $s->name,
            'phone' => $s->phone,
            'address' => $s->address,
            'note' => $s->note,
            'type_location' => $s->type_location,
            'user_id' => $s->user_id,
            'created_at' => $s->created_at?->toIso8601String(),
            'updated_at' => $s->updated_at?->toIso8601String(),
        ];
        if ($s->relationLoaded('creator')) {
            $out['creator'] = self::creatorBrief($s->creator);
        }

        return $out;
    }

    public static function purchaseItem(purchaseitem $i): array
    {
        $i->loadMissing(['product.unit', 'unit']);

        $out = [
            'uuid' => $i->uuid,
            'purchase_uuid' => $i->purchase_uuid,
            'product_uuid' => $i->product_uuid,
            'unit_uuid' => $i->unit_uuid,
            'quantity' => $i->quantity,
            'price' => $i->price,
            'type_location' => $i->type_location,
            'user_id' => $i->user_id,
            'product' => $i->product ? self::product($i->product) : null,
            'unit' => $i->unit ? self::unit($i->unit) : null,
        ];
        if ($i->relationLoaded('creator')) {
            $out['creator'] = self::creatorBrief($i->creator);
        }

        return $out;
    }

    public static function purchase(Purchase $p): array
    {
        $p->loadMissing(['supplier', 'items.product.unit', 'items.unit']);

        $out = [
            'uuid' => $p->uuid,
            'invoice_number' => $p->invoice_number,
            'supplier_uuid' => $p->supplier_uuid,
            'date' => $p->date?->format('Y-m-d'),
            'type_location' => $p->type_location,
            'user_id' => $p->user_id,
            'supplier' => $p->supplier ? self::supplier($p->supplier) : null,
            'items' => $p->items->map(fn ($i) => self::purchaseItem($i))->values()->all(),
            'created_at' => $p->created_at?->toIso8601String(),
            'updated_at' => $p->updated_at?->toIso8601String(),
        ];
        if ($p->relationLoaded('creator')) {
            $out['creator'] = self::creatorBrief($p->creator);
        }

        return $out;
    }

    public static function returnItem(ReturnItem $i): array
    {
        $i->loadMissing(['product.unit', 'unit']);

        $out = [
            'uuid' => $i->uuid,
            'return_uuid' => $i->return_uuid,
            'product_uuid' => $i->product_uuid,
            'unit_uuid' => $i->unit_uuid,
            'quantity' => $i->quantity,
            'type_location' => $i->type_location,
            'user_id' => $i->user_id,
            'product' => $i->product ? self::product($i->product) : null,
            'unit' => $i->unit ? self::unit($i->unit) : null,
        ];
        if ($i->relationLoaded('creator')) {
            $out['creator'] = self::creatorBrief($i->creator);
        }

        return $out;
    }

    public static function warehouseReturn(ReturnModel $r): array
    {
        $r->loadMissing(['supplier', 'items.product.unit', 'items.unit']);

        $out = [
            'uuid' => $r->uuid,
            'date' => $r->date?->format('Y-m-d'),
            'type' => $r->type,
            'supplier_uuid' => $r->supplier_uuid,
            'note' => $r->note,
            'type_location' => $r->type_location,
            'user_id' => $r->user_id,
            'supplier' => $r->supplier ? self::supplier($r->supplier) : null,
            'items' => $r->items->map(fn ($i) => self::returnItem($i))->values()->all(),
            'created_at' => $r->created_at?->toIso8601String(),
            'updated_at' => $r->updated_at?->toIso8601String(),
        ];
        if ($r->relationLoaded('creator')) {
            $out['creator'] = self::creatorBrief($r->creator);
        }

        return $out;
    }
}
