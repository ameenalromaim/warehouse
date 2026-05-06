<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * توليد رقم فاتورة تلقائي
     * مثال:
     * INV-20260502-00001
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
    public function index()
    {
        $purchases = Purchase::with([
            'supplier',
            'items.product',
            'items.unit',
        ])
            ->latest()
            ->paginate(10);

        return response()->json($purchases);
    }

    /**
     * حفظ فاتورة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_uuid' => 'required|uuid|exists:suppliers,uuid',
            'date' => 'nullable|date',

            'items' => 'required|array|min:1',
            'items.*.product_uuid' => 'required|uuid|exists:product,uuid',
            'items.*.unit_uuid' => 'required|uuid|exists:units,uuid',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        return DB::transaction(function () use ($request) {

            $purchase = Purchase::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'supplier_uuid' => $request->supplier_uuid,
                'date' => $request->date ?? now(),
            ]);

            foreach ($request->items as $item) {
                purchaseitem::create([
                    'purchase_uuid' => $purchase->uuid,
                    'product_uuid' => $item['product_uuid'],
                    'unit_uuid' => $item['unit_uuid'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'] ?? 0,
                ]);
            }

            return response()->json([
                'message' => 'تم حفظ الفاتورة بنجاح',
                'data' => $purchase->load([
                    'supplier',
                    'items.product',
                    'items.unit',
                ]),
            ], 201);
        });
    }

    /**
     * عرض فاتورة واحدة
     */
    public function show(Purchase $purchase)
    {
        $purchase->load([
            'supplier',
            'items.product',
            'items.unit',
        ]);

        return response()->json($purchase);
    }

    /**
     * تعديل فاتورة
     */
    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'supplier_uuid' => 'required|uuid|exists:suppliers,uuid',
            'date' => 'nullable|date',

            'items' => 'required|array|min:1',
            'items.*.product_uuid' => 'required|uuid|exists:product,uuid',
            'items.*.unit_uuid' => 'required|uuid|exists:units,uuid',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request, $purchase) {

            $purchase->update([
                'supplier_uuid' => $request->supplier_uuid,
                'date' => $request->date ?? $purchase->date,
            ]);

            purchaseitem::where('purchase_uuid', $purchase->uuid)->delete();

            foreach ($request->items as $item) {
                purchaseitem::create([
                    'purchase_uuid' => $purchase->uuid,
                    'product_uuid' => $item['product_uuid'],
                    'unit_uuid' => $item['unit_uuid'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'] ?? 0,
                ]);
            }

            return response()->json([
                'message' => 'تم تعديل الفاتورة بنجاح',
                'data' => $purchase->load([
                    'supplier',
                    'items.product',
                    'items.unit',
                ]),
            ]);
        });
    }

    /**
     * حذف فاتورة
     */
    public function destroy(Purchase $purchase)
    {
        return DB::transaction(function () use ($purchase) {

            purchaseitem::where('purchase_uuid', $purchase->uuid)->delete();

            $purchase->delete();

            return response()->json([
                'message' => 'تم حذف الفاتورة بنجاح',
            ]);
        });
    }
}