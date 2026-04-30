<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\purchaseitem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    private function generateInvoiceNumber(): string
    {
        $today = now()->format('Ymd');
        $lastId = (int) Purchase::max('id');
        $next = str_pad((string) ($lastId + 1), 5, '0', STR_PAD_LEFT);
        return "INV-{$today}-{$next}";
    }

    // 🔷 عرض الفواتير
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'items.product', 'items.unit'])
            ->latest()
            ->paginate(10);

        return response()->json($purchases);
    }

    // 🔷 إضافة فاتورة
    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'nullable|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:product,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $invoiceNumber = trim((string) $request->invoice_number);
            if ($invoiceNumber === '') {
                $invoiceNumber = $this->generateInvoiceNumber();
            }

            $purchase = Purchase::create([
                'invoice_number' => $invoiceNumber,
                'supplier_id' => $request->supplier_id,
                'date' => $request->date ?? now(),
                'notes' => $request->notes,
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $price = isset($item['price']) ? (float) $item['price'] : 0.0;
                $itemTotal = (float) $item['quantity'] * $price;

                purchaseitem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                ]);

                $total += $itemTotal;
            }

            $purchase->update(['total' => $total]);

            return response()->json([
                'message' => 'تم حفظ الفاتورة',
                'data' => $purchase->load('items')
            ], 201);
        });
    }

    // 🔷 عرض فاتورة واحدة
    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'items.product', 'items.unit'])
            ->findOrFail($id);

        return response()->json($purchase);
    }

    // 🔷 حذف فاتورة
    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->delete();

        return response()->json([
            'message' => 'تم الحذف'
        ]);
    }
}
