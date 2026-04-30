<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReturnModel;
use App\Models\ReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    // 🔷 قائمة المردودات
    public function index(Request $request)
    {
        $q = ReturnModel::with(['items.product', 'items.unit'])
            ->latest();

        // فلترة اختيارية
        if ($request->type) {
            $q->where('type', $request->type); // normal | damage
        }
        if ($request->from) {
            $q->whereDate('date', '>=', $request->from);
        }
        if ($request->to) {
            $q->whereDate('date', '<=', $request->to);
        }

        return response()->json($q->paginate(10));
    }

    // 🔷 إنشاء مردود
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:normal,damage',
            'note' => 'nullable|string',
            // اختياري لاحقًا:
            // 'sale_id' => 'nullable|exists:sales,id',
            // 'purchase_id' => 'nullable|exists:purchases,id',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:product,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        return DB::transaction(function () use ($request) {

            $return = ReturnModel::create([
                'date' => $request->date,
                'type' => $request->type,
                'note' => $request->note,
                // 'sale_id' => $request->sale_id,
                // 'purchase_id' => $request->purchase_id,
            ]);

            $itemsPayload = [];
            foreach ($request->items as $item) {
                $itemsPayload[] = new ReturnItem([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            $return->items()->saveMany($itemsPayload);

            // 🔥 إن كان عندك جدول مخزون فعلي (stocks) عدّل هنا:
            // if ($return->type === 'normal') => زيادة
            // if ($return->type === 'damage') => إنقاص

            return response()->json([
                'message' => 'تم حفظ المردود',
                'data' => $return->load('items.product', 'items.unit'),
            ], 201);
        });
    }

    // 🔷 عرض مردود واحد
    public function show($id)
    {
        $return = ReturnModel::with(['items.product', 'items.unit'])
            ->findOrFail($id);

        return response()->json($return);
    }

    // 🔷 حذف
    public function destroy($id)
    {
        $return = ReturnModel::findOrFail($id);
        $return->delete();

        return response()->json([
            'message' => 'تم الحذف'
        ]);
    }

    // 🔷 تحديث (اختياري لكن مهم)
    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:normal,damage',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:product,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        return DB::transaction(function () use ($request, $id) {

            $return = ReturnModel::findOrFail($id);

            $return->update([
                'date' => $request->date,
                'type' => $request->type,
                'note' => $request->note,
            ]);

            // حذف التفاصيل القديمة وإعادة إدخالها
            $return->items()->delete();

            $itemsPayload = [];
            foreach ($request->items as $item) {
                $itemsPayload[] = new ReturnItem([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            $return->items()->saveMany($itemsPayload);

            return response()->json([
                'message' => 'تم التحديث',
                'data' => $return->load('items.product', 'items.unit'),
            ]);
        });
    }
}
