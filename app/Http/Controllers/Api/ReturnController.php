<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Support\ApiPresenter;
use App\Models\product;
use App\Models\ReturnItem;
use App\Models\ReturnModel;
use App\Models\suppliers;
use App\Support\BranchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $q = ReturnModel::with(['items.product', 'items.unit', 'supplier'])
            ->forUserBranch($request->user())
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

        return response()->json(
            $q->paginate(10)->through(fn ($r) => ApiPresenter::warehouseReturn($r))
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:normal,damage',
            'supplier_uuid' => 'nullable|uuid|exists:suppliers,uuid',
            'note' => 'nullable|string',
            'type_location' => 'nullable|string|max:255',

            'items' => 'required|array|min:1',
            'items.*.product_uuid' => 'required|uuid|exists:product,uuid',
            'items.*.unit_uuid' => 'required|uuid|exists:units,uuid',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $this->assertBranchUserHasBranch($request);

        $loc = BranchData::locationForWrite($request);

        if ($request->filled('supplier_uuid')
            && ! suppliers::query()->forUserBranch($request->user())->where('uuid', $request->supplier_uuid)->exists()) {
            throw ValidationException::withMessages([
                'supplier_uuid' => [__('المورد غير متاح لهذا الفرع.')],
            ]);
        }

        $productUuids = collect($request->items)->pluck('product_uuid')->unique()->values();
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

            $return = ReturnModel::create([
                'date' => $request->date,
                'type' => $request->type,
                'supplier_uuid' => $request->supplier_uuid,
                'note' => $request->note,
                'type_location' => $loc,
            ]);

            $itemsPayload = [];
            foreach ($request->items as $item) {
                $itemsPayload[] = new ReturnItem([
                    'product_uuid' => $item['product_uuid'],
                    'unit_uuid' => $item['unit_uuid'],
                    'quantity' => $item['quantity'],
                    'type_location' => $loc,
                ]);
            }

            $return->items()->saveMany($itemsPayload);

            return response()->json([
                'message' => 'تم حفظ المردود',
                'data' => ApiPresenter::warehouseReturn($return->load('items.product', 'items.unit', 'supplier')),
            ], 201);
        });
    }

    public function show(Request $request, ReturnModel $warehouse_return)
    {
        $this->authorizeBranchRecord($warehouse_return);

        $warehouse_return->load(['items.product', 'items.unit', 'supplier']);

        return response()->json(ApiPresenter::warehouseReturn($warehouse_return));
    }

    public function destroy(Request $request, ReturnModel $warehouse_return)
    {
        $this->authorizeBranchRecord($warehouse_return);

        $warehouse_return->delete();

        return response()->json([
            'message' => 'تم الحذف',
        ]);
    }

    public function update(Request $request, ReturnModel $warehouse_return)
    {
        $this->authorizeBranchRecord($warehouse_return);

        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:normal,damage',
            'supplier_uuid' => 'nullable|uuid|exists:suppliers,uuid',
            'note' => 'nullable|string',
            'type_location' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_uuid' => 'required|uuid|exists:product,uuid',
            'items.*.unit_uuid' => 'required|uuid|exists:units,uuid',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $this->assertBranchUserHasBranch($request);

        $loc = BranchData::locationForWrite($request);

        if ($request->filled('supplier_uuid')
            && ! suppliers::query()->forUserBranch($request->user())->where('uuid', $request->supplier_uuid)->exists()) {
            throw ValidationException::withMessages([
                'supplier_uuid' => [__('المورد غير متاح لهذا الفرع.')],
            ]);
        }

        $productUuids = collect($request->items)->pluck('product_uuid')->unique()->values();
        $countOk = product::query()
            ->forUserBranch($request->user())
            ->whereIn('uuid', $productUuids)
            ->count();

        if ($countOk !== $productUuids->count()) {
            throw ValidationException::withMessages([
                'items' => [__('أحد الأصناف غير تابع لفرعك.')],
            ]);
        }

        return DB::transaction(function () use ($request, $warehouse_return, $loc) {

            $warehouse_return->update([
                'date' => $request->date,
                'type' => $request->type,
                'supplier_uuid' => $request->supplier_uuid,
                'note' => $request->note,
                'type_location' => $loc,
            ]);

            $warehouse_return->items()->delete();

            $itemsPayload = [];
            foreach ($request->items as $item) {
                $itemsPayload[] = new ReturnItem([
                    'product_uuid' => $item['product_uuid'],
                    'unit_uuid' => $item['unit_uuid'],
                    'quantity' => $item['quantity'],
                    'type_location' => $loc,
                ]);
            }

            $warehouse_return->items()->saveMany($itemsPayload);

            return response()->json([
                'message' => 'تم التحديث',
                'data' => ApiPresenter::warehouseReturn($warehouse_return->load('items.product', 'items.unit', 'supplier')),
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
