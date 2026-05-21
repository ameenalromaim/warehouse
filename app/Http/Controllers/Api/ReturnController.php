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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $q = ReturnModel::with([
            'items.product',
            'items.unit',
            'supplier',
            'creator'
        ])
            ->forUserBranch($request->user())
            ->latest();

        if ($request->type) {
            $q->where('type', $request->type);
        }

        if ($request->filled('supplier')) {
            $s = $request->supplier;

            $q->whereHas('supplier', function ($x) use ($s) {
                $x->where('name', 'like', '%' . $s . '%');
            });
        }

        if ($request->filled('product')) {
            $p = $request->product;

            $q->whereHas('items.product', function ($x) use ($p) {
                $x->where('name', 'like', '%' . $p . '%');
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
            $q->paginate(10)->through(
                fn($r) => ApiPresenter::warehouseReturn($r)
            )
        );
    }

    public function store(Request $request)
    {
        Log::info('Return Store Request', [
            'user_id' => $request->user()?->id,
            'payload' => $request->all(),
        ]);

        try {

            $request->validate([
                'uuid' => 'nullable|uuid',
                'date' => 'required|date',
                'type' => 'required|in:normal,damage',
                'supplier_uuid' => 'nullable|uuid|exists:suppliers,uuid',
                'note' => 'nullable|string',
                'type_location' => 'nullable|string|max:255',

                'items' => 'required|array|min:1',
                'items.*.uuid' => 'nullable|uuid',
                'items.*.product_uuid' => 'required|uuid|exists:product,uuid',
                'items.*.unit_uuid' => 'required|uuid|exists:units,uuid',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.note' => 'nullable|string',
            ]);

            $this->assertBranchUserHasBranch($request);

            $loc = BranchData::locationForWrite($request);
              
            //التحقق من المورد حسب الفرع
            //تمنع المستخدم من اختيار:
            // مورد ليس تابع لفرعه
            // if (
            //     $request->filled('supplier_uuid') &&
            //     !suppliers::query()
            //         ->forUserBranch($request->user())
            //         ->where('uuid', $request->supplier_uuid)
            //         ->exists()
            // ) {
            //     throw ValidationException::withMessages([
            //         'supplier_uuid' => [__('المورد غير متاح لهذا الفرع.')],
            //     ]);
            // }
            //التحقق من الأصناف حسب الفرع
            //تضمن أن:
           // كل المنتجات داخل الطلب تخص نفس الفرع
            // $productUuids = collect($request->items)
            //     ->pluck('product_uuid')
            //     ->unique()
            //     ->values();

            // $countOk = product::query()
            //     ->forUserBranch($request->user())
            //     ->whereIn('uuid', $productUuids)
            //     ->count();
            // $countOk = product::query();

            // Log::info('Product Validation', [
            //     'requested_products' => $productUuids,
            //     'found_count' => $countOk,
            //     'expected_count' => $productUuids->count(),
            //     'user_id' => $request->user()?->id,
            // ]);

            // if ($countOk !== $productUuids->count()) {

            //     Log::warning('Products Not Allowed For Branch', [
            //         'requested_products' => $productUuids,
            //         'found_count' => $countOk,
            //         'expected_count' => $productUuids->count(),
            //         'user_id' => $request->user()?->id,
            //     ]);

            //     throw ValidationException::withMessages([
            //         'items' => [__('note item')],
            //     ]);
            // }

            return DB::transaction(function () use ($request, $loc) {

                Log::info('Creating Return');

                $return = ReturnModel::create([
                    'uuid' => $request->uuid ?? (string) Str::uuid(),
                    'date' => $request->date,
                    'type' => $request->type,
                    'supplier_uuid' => $request->supplier_uuid,
                    'note' => $request->note,
                    'type_location' => $loc,
                    'user_id' => $request->user()->id,
                ]);

                Log::info('Return Created', [
                    'return_id' => $return->id,
                    'return_uuid' => $return->uuid,
                ]);

                $itemsPayload = [];

                foreach ($request->items as $item) {

                    Log::info('Preparing Return Item', [
                        'item' => $item,
                    ]);

                    $itemsPayload[] = new ReturnItem([
                        'uuid' => $item['uuid'] ?? (string) Str::uuid(),
                        'product_uuid' => $item['product_uuid'],
                        'unit_uuid' => $item['unit_uuid'],
                        'quantity' => $item['quantity'],
                        'note' => $item['note'] ?? null,
                        'type_location' => $loc,
                        'user_id' => $request->user()->id,
                    ]);
                }

                $return->items()->saveMany($itemsPayload);

                Log::info('Return Items Saved', [
                    'count' => count($itemsPayload),
                ]);

                return response()->json([
                    'message' => 'تم حفظ المردود',
                    'data' => ApiPresenter::warehouseReturn(
                        $return->load(
                            'items.product',
                            'items.unit',
                            'supplier',
                            'creator'
                        )
                    ),
                ], 201);
            });

        } catch (\Throwable $e) {

            Log::error('Return Store Failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user_id' => $request->user()?->id,
                'payload' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'فشل حفظ المردود',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, ReturnModel $warehouse_return)
    {
        $this->authorizeBranchRecord($warehouse_return);

        $warehouse_return->load([
            'items.product',
            'items.unit',
            'supplier',
            'creator',
        ]);

        return response()->json(
            ApiPresenter::warehouseReturn($warehouse_return)
        );
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
            'items.*.uuid' => 'nullable|uuid',
            'items.*.product_uuid' => 'required|uuid|exists:product,uuid',
            'items.*.unit_uuid' => 'required|uuid|exists:units,uuid',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.note' => 'nullable|string',
        ]);

        $this->assertBranchUserHasBranch($request);

        $loc = BranchData::locationForWrite($request);

        return DB::transaction(function () use (
            $request,
            $warehouse_return,
            $loc
        ) {

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
                    'uuid' => $item['uuid'] ?? (string) Str::uuid(),
                    'product_uuid' => $item['product_uuid'],
                    'unit_uuid' => $item['unit_uuid'],
                    'quantity' => $item['quantity'],
                    'note' => $item['note'] ?? null,
                    'type_location' => $loc,
                    'user_id' => $request->user()->id,
                ]);
            }

            $warehouse_return->items()->saveMany($itemsPayload);

            return response()->json([
                'message' => 'تم التحديث',
                'data' => ApiPresenter::warehouseReturn(
                    $warehouse_return->load(
                        'items.product',
                        'items.unit',
                        'supplier',
                        'creator'
                    )
                ),
            ]);
        });
    }

    private function assertBranchUserHasBranch(Request $request): void
    {
        $user = $request->user();

        if (
            $user &&
            $user->isBranchUser() &&
            $user->branchScopeKey() === null
        ) {
            throw ValidationException::withMessages([
                'type_location' => [__('لم يُعرَّف فرع لهذا المستخدم.')],
            ]);
        }
    }
}