<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReturnItem;
use App\Models\ReturnModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $q = ReturnModel::with(['items.product', 'items.unit', 'supplier'])
            ->latest();

        if ($request->type) {
            $q->where('type', $request->type);
        }
        if ($request->filled('supplier')) {
            $s = $request->supplier;
            $q->whereHas('supplier', function ($x) use ($s) {
                $x->where('name', 'like', '%'.$s.'%');
            });
        }
        if ($request->filled('product')) {
            $p = $request->product;
            $q->whereHas('items.product', function ($x) use ($p) {
                $x->where('name', 'like', '%'.$p.'%');
            });
        }
        if ($request->filled('date')) {
            $q->whereDate('date', $request->date);
        } else {
            if ($request->from) {
                $q->whereDate('date', '>=', $request->from);
            }
            if ($request->to) {
                $q->whereDate('date', '<=', $request->to);
            }
        }

        return response()->json($q->paginate(10));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:normal,damage',
            'supplier_uuid' => 'nullable|uuid|exists:suppliers,uuid',
            'note' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.product_uuid' => 'required|uuid|exists:product,uuid',
            'items.*.unit_uuid' => 'required|uuid|exists:units,uuid',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        return DB::transaction(function () use ($request) {

            $return = ReturnModel::create([
                'date' => $request->date,
                'type' => $request->type,
                'supplier_uuid' => $request->supplier_uuid,
                'note' => $request->note,
            ]);

            $itemsPayload = [];
            foreach ($request->items as $item) {
                $itemsPayload[] = new ReturnItem([
                    'product_uuid' => $item['product_uuid'],
                    'unit_uuid' => $item['unit_uuid'],
                    'quantity' => $item['quantity'],
                ]);
            }

            $return->items()->saveMany($itemsPayload);

            return response()->json([
                'message' => 'تم حفظ المردود',
                'data' => $return->load('items.product', 'items.unit', 'supplier'),
            ], 201);
        });
    }

    public function show(ReturnModel $warehouse_return)
    {
        $warehouse_return->load(['items.product', 'items.unit', 'supplier']);

        return response()->json($warehouse_return);
    }

    public function destroy(ReturnModel $warehouse_return)
    {
        $warehouse_return->delete();

        return response()->json([
            'message' => 'تم الحذف',
        ]);
    }

    public function update(Request $request, ReturnModel $warehouse_return)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:normal,damage',
            'supplier_uuid' => 'nullable|uuid|exists:suppliers,uuid',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_uuid' => 'required|uuid|exists:product,uuid',
            'items.*.unit_uuid' => 'required|uuid|exists:units,uuid',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        return DB::transaction(function () use ($request, $warehouse_return) {

            $warehouse_return->update([
                'date' => $request->date,
                'type' => $request->type,
                'supplier_uuid' => $request->supplier_uuid,
                'note' => $request->note,
            ]);

            $warehouse_return->items()->delete();

            $itemsPayload = [];
            foreach ($request->items as $item) {
                $itemsPayload[] = new ReturnItem([
                    'product_uuid' => $item['product_uuid'],
                    'unit_uuid' => $item['unit_uuid'],
                    'quantity' => $item['quantity'],
                ]);
            }

            $warehouse_return->items()->saveMany($itemsPayload);

            return response()->json([
                'message' => 'تم التحديث',
                'data' => $warehouse_return->load('items.product', 'items.unit', 'supplier'),
            ]);
        });
    }
}
