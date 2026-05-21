<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Support\ApiPresenter;
use App\Models\product;
use App\Support\BranchData;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * إضافة منتج جديد
     */
    public function store(Request $request)
    {
        $this->requireBranchWhenNeeded($request);

        $validated = $request->validate([
            'uuid' => 'required|uuid|unique:product,uuid',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:product,code',
            'description' => 'nullable|string',
            'unit_uuid' => 'required|uuid|exists:units,uuid',
            'type_location' => 'nullable|string|max:255',
        ]);

        $loc = BranchData::locationForWrite($request);

        $exists = product::query()
            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($validated['name']))])
            ->where('unit_uuid', $validated['unit_uuid'])
            ->when($loc !== null, fn ($q) => $q->where('type_location', $loc))
            ->when($loc === null, fn ($q) => $q->whereNull('type_location'))
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'هذا المنتج موجود مسبقاً',
            ], 422);
        }

        $code = $validated['code'] ?? null;

        if ($code === null || $code === '') {
            $code = $this->generateUniqueProductCode();
        }

        $row = product::create([
            'uuid' => $validated['uuid'],
            'name' => $validated['name'],
            'code' => $code,
            'description' => $validated['description'] ?? null,
            'unit_uuid' => $validated['unit_uuid'],
            'type_location' => $loc,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'تم إنشاء المنتج بنجاح',
            'data' => ApiPresenter::product($row->load(['unit', 'creator'])),
        ], 201);
    }

    /**
     * عرض جميع المنتجات
     */
    public function index(Request $request)
    {
        $rows = product::query()
            ->with(['unit', 'creator'])
            // ->forUserBranch($request->user())
            ->latest()
            ->get();

        return response()->json(
            $rows->map(fn ($p) => ApiPresenter::product($p))->values()
        );
    }

    /**
     * عرض منتج واحد
     */
    public function show(Request $request, product $product)
    {
        $this->authorizeBranchRecord($product);

        return response()->json(ApiPresenter::product($product->load(['unit', 'creator'])));
    }

    /**
     * تعديل منتج
     */
    public function update(Request $request, product $product)
    {
        $this->authorizeBranchRecord($product);
        $this->requireBranchWhenNeeded($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['nullable', 'string', 'max:255', Rule::unique('product', 'code')->ignore($product->uuid, 'uuid')],
            'description' => 'nullable|string',
            'unit_uuid' => 'required|uuid|exists:units,uuid',
            'type_location' => 'nullable|string|max:255',
        ]);

        $loc = BranchData::locationForWrite($request);

        $exists = product::query()
            ->where('uuid', '!=', $product->uuid)
            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($validated['name']))])
            ->where('unit_uuid', $validated['unit_uuid'])
            ->when($loc !== null, fn ($q) => $q->where('type_location', $loc))
            ->when($loc === null, fn ($q) => $q->whereNull('type_location'))
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'هذا المنتج موجود مسبقاً',
            ], 422);
        }

        $code = $validated['code'] ?? null;

        if ($code === null || $code === '') {
            $code = $product->code ?: $this->generateUniqueProductCode();
        }

        $product->update([
            'name' => $validated['name'],
            'code' => $code,
            'description' => $validated['description'] ?? null,
            'unit_uuid' => $validated['unit_uuid'],
            'type_location' => $loc,
        ]);

        return response()->json([
            'message' => 'تم تعديل المنتج بنجاح',
            'data' => ApiPresenter::product($product->load(['unit', 'creator'])),
        ]);
    }

    /**
     * حذف منتج
     */
    public function destroy(Request $request, product $product)
    {
        $this->authorizeBranchRecord($product);

        $product->delete();

        return response()->json([
            'message' => 'تم حذف المنتج بنجاح',
        ]);
    }

    /**
     * توليد كود منتج تلقائي
     */
    private function generateUniqueProductCode(): string
    {
        do {
            $code = 'PRD-'.strtoupper(Str::random(8));
        } while (product::where('code', $code)->exists());

        return $code;
    }

    private function requireBranchWhenNeeded(Request $request): void
    {
        $user = $request->user();

        if ($user && $user->isBranchUser() && $user->branchScopeKey() === null) {
            throw ValidationException::withMessages([
                'type_location' => [__('لم يُعرَّف فرع لهذا المستخدم.')],
            ]);
        }
    }
}
