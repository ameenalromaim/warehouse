<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Support\ApiPresenter;
use App\Models\product;
use App\Models\Purchase;
use App\Models\purchaseitem;
use App\Models\suppliers;
use App\Support\BranchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    /**
     * توليد رقم فاتورة تلقائي
     */
    private function generateInvoiceNumber(): string
    {
        $today = now()->format('Ymd');

        $last = Purchase::latest('id')->first();
        $nextId = $last ? $last->id + 1 : 1;

        $next = str_pad((string) $nextId, 5, '0', STR_PAD_LEFT);

        return "INV-{$today}-{$next}";
    }

    /**
     * عرض جميع الفواتير
     */
    public function index(Request $request)
    {
        $purchases = Purchase::with([
            'supplier',
            'items.product',
            'items.unit',
            'creator',
        ])
            ->forUserBranch($request->user())
            ->latest()
            ->paginate(10);

        return response()->json(
            $purchases->through(fn ($p) => ApiPresenter::purchase($p))
        );
    }

    /**
     * حفظ فاتورة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'uuid' => 'required|uuid|unique:purchase,uuid',
            'supplier_uuid' => 'required|uuid|exists:suppliers,uuid',
            'date' => 'nullable|date',
            'type_location' => 'nullable|string|max:255',

            'items' => 'required|array|min:1',

            'items.*.uuid' => 'required|uuid|distinct',
            'items.*.product_uuid' => 'required|uuid|exists:product,uuid',
            'items.*.unit_uuid' => 'required|uuid|exists:units,uuid',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'nullable|numeric|min:0',
        ]);

        $this->assertBranchUserHasBranch($request);

        $loc = BranchData::locationForWrite($request);

        if (! suppliers::query()
            ->forUserBranch($request->user())
            ->where('uuid', $request->supplier_uuid)
            ->exists()) {
            throw ValidationException::withMessages([
                'supplier_uuid' => [__('المورد غير متاح لهذا الفرع.')],
            ]);
        }

        $productUuids = collect($request->items)
            ->pluck('product_uuid')
            ->unique()
            ->values();

        $countOk = product::query()
            ->forUserBranch($request->user())
            ->whereIn('uuid', $productUuids)
            ->count();

        if ($countOk !== $productUuids->count()) {
            throw ValidationException::withMessages([
                'items' => [__('أحد الأصناف غير تابع لفرعك.')],
            ]);
        }

        return DB::transaction(function () use ($request, $loc) {

            $purchase = Purchase::create([
                'uuid' => $request->uuid,
                'invoice_number' => $this->generateInvoiceNumber(),
                'supplier_uuid' => $request->supplier_uuid,
                'date' => $request->date ?? now(),
                'type_location' => $loc,
                'user_id' => $request->user()->id,
            ]);

            foreach ($request->items as $item) {
                purchaseitem::create([
                    'uuid' => $item['uuid'],
                    'purchase_uuid' => $request->uuid,
                    'product_uuid' => $item['product_uuid'],
                    'unit_uuid' => $item['unit_uuid'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'] ?? 0,
                    'type_location' => $loc,
                    'user_id' => $request->user()->id,
                    'note' => $item['note'] ?? null,
                ]);
            }

            return response()->json([
                'message' => 'تم حفظ الفاتورة بنجاح',
                'data' => ApiPresenter::purchase($purchase->load([
                    'supplier',
                    'items.product',
                    'items.unit',
                    'creator',
                ])),
            ], 201);
        });
    }

    /**
     * عرض فاتورة واحدة
     */
    public function show(Request $request, Purchase $purchase)
    {
        $this->authorizeBranchRecord($purchase);

        $purchase->load([
            'supplier',
            'items.product',
            'items.unit',
            'creator',
        ]);

        return response()->json(ApiPresenter::purchase($purchase));
    }

    /**
     * تعديل فاتورة
     */
    public function update(Request $request, Purchase $purchase)
    {
        $this->authorizeBranchRecord($purchase);

        $request->validate([
            'supplier_uuid' => 'required|uuid|exists:suppliers,uuid',
            'date' => 'nullable|date',
            'type_location' => 'nullable|string|max:255',

            'items' => 'required|array|min:1',

            'items.*.uuid' => 'required|uuid|distinct',
            'items.*.product_uuid' => 'required|uuid|exists:product,uuid',
            'items.*.unit_uuid' => 'required|uuid|exists:units,uuid',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'nullable|numeric|min:0',
        ]);

        $this->assertBranchUserHasBranch($request);

        $loc = BranchData::locationForWrite($request);

        if (! suppliers::query()
            ->forUserBranch($request->user())
            ->where('uuid', $request->supplier_uuid)
            ->exists()) {
            throw ValidationException::withMessages([
                'supplier_uuid' => [__('المورد غير متاح لهذا الفرع.')],
            ]);
        }

        $productUuids = collect($request->items)
            ->pluck('product_uuid')
            ->unique()
            ->values();

        $countOk = product::query()
            ->forUserBranch($request->user())
            ->whereIn('uuid', $productUuids)
            ->count();

        if ($countOk !== $productUuids->count()) {
            throw ValidationException::withMessages([
                'items' => [__('أحد الأصناف غير تابع لفرعك.')],
            ]);
        }

        return DB::transaction(function () use ($request, $purchase, $loc) {

            $purchase->update([
                'supplier_uuid' => $request->supplier_uuid,
                'date' => $request->date ?? $purchase->date,
                'type_location' => $loc,
            ]);

            purchaseitem::where('purchase_uuid', $purchase->uuid)->delete();

            foreach ($request->items as $item) {
                purchaseitem::create([
                    'uuid' => $item['uuid'],
                    'purchase_uuid' => $purchase->uuid,
                    'product_uuid' => $item['product_uuid'],
                    'unit_uuid' => $item['unit_uuid'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'] ?? 0,
                    'type_location' => $loc,
                    'user_id' => $request->user()->id,
                    'note' => $item['note'] ?? null,
                ]);
            }

            return response()->json([
                'message' => 'تم تعديل الفاتورة بنجاح',
                'data' => ApiPresenter::purchase($purchase->load([
                    'supplier',
                    'items.product',
                    'items.unit',
                    'creator',
                ])),
            ]);
        });
    }

    /**
     * حذف فاتورة
     */
    public function destroy(Request $request, Purchase $purchase)
    {
        $this->authorizeBranchRecord($purchase);

        return DB::transaction(function () use ($purchase) {

            purchaseitem::where('purchase_uuid', $purchase->uuid)->delete();

            $purchase->delete();

            return response()->json([
                'message' => 'تم حذف الفاتورة بنجاح',
            ]);
        });
    }

    private function assertBranchUserHasBranch(Request $request): void
    {
        $user = $request->user();

        if ($user && $user->isBranchUser() && $user->branchScopeKey() === null) {
            throw ValidationException::withMessages([
                'type_location' => [__('لم يُعرَّف فرع لهذا المستخدم.')],
            ]);
        }
    }
}