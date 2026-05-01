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

        $next = str_pad($nextId, 5, '0', STR_PAD_LEFT);

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
            'items.unit'
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
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'nullable|date',
            'notes' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:product,id',
            'items.*.unit_id' => 'nullable|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        return DB::transaction(function () use ($request) {

            $purchase = Purchase::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'supplier_id' => $request->supplier_id,
                'date' => $request->date ?? now(),
                'notes' => $request->notes,
                'total' => 0,
            ]);

            foreach ($request->items as $item) {

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'] ?? null,
                    'quantity' => $item['quantity'],
                ]);
            }

            return response()->json([
                'message' => 'تم حفظ الفاتورة بنجاح',
                'data' => $purchase->load([
                    'supplier',
                    'items.product',
                    'items.unit'
                ])
            ], 201);
        });
    }

    /**
     * عرض فاتورة واحدة
     */
    public function show($id)
    {
        $purchase = Purchase::with([
            'supplier',
            'items.product',
            'items.unit'
        ])->findOrFail($id);

        return response()->json($purchase);
    }

    /**
     * تعديل فاتورة
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'nullable|date',
            'notes' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:product,id',
            'items.*.unit_id' => 'nullable|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        return DB::transaction(function () use ($request, $id) {

            $purchase = Purchase::findOrFail($id);

            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'date' => $request->date ?? $purchase->date,
                'notes' => $request->notes,
                'total' => 0,
            ]);

            // حذف الأصناف القديمة
            PurchaseItem::where('purchase_id', $purchase->id)->delete();

            // إضافة الأصناف الجديدة
            foreach ($request->items as $item) {

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'] ?? null,
                    'quantity' => $item['quantity'],
                ]);
            }

            return response()->json([
                'message' => 'تم تعديل الفاتورة بنجاح',
                'data' => $purchase->load([
                    'supplier',
                    'items.product',
                    'items.unit'
                ])
            ]);
        });
    }

    /**
     * حذف فاتورة
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {

            $purchase = Purchase::findOrFail($id);

            // حذف الأصناف أولاً
            PurchaseItem::where('purchase_id', $purchase->id)->delete();

            // حذف الفاتورة
            $purchase->delete();

            return response()->json([
                'message' => 'تم حذف الفاتورة بنجاح'
            ]);
        });
    }
}