<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * إضافة منتج جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:product,code',
            'description' => 'nullable|string',
            'unit_uuid' => 'required|uuid|exists:units,uuid',
        ]);
        $exists = product::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($validated['name']))])
            ->where('unit_uuid', $validated['unit_uuid'])
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

        $row = Product::create([
            'name' => $validated['name'],
            'code' => $code,
            'description' => $validated['description'] ?? null,
            'unit_uuid' => $validated['unit_uuid'],
        ]);

        $row->load('unit');

        return response()->json([
            'message' => 'تم إنشاء المنتج بنجاح',
            'data' => $row,
        ], 201);
    }

    /**
     * عرض جميع المنتجات
     */
    public function index()
    {
        return response()->json(
            Product::with('unit')->latest()->get()
        );
    }

    /**
     * عرض منتج واحد
     */
    public function show(product $product)
    {
        $product->load('unit');

        return response()->json($product);
    }

    /**
     * تعديل منتج
     */
    public function update(Request $request, product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['nullable', 'string', 'max:255', Rule::unique('product', 'code')->ignore($product->uuid, 'uuid')],
            'description' => 'nullable|string',
            'unit_uuid' => 'required|uuid|exists:units,uuid',
        ]);

        $code = $validated['code'] ?? null;

        if ($code === null || $code === '') {
            $code = $product->code ?: $this->generateUniqueProductCode();
        }

        $product->update([
            'name' => $validated['name'],
            'code' => $code,
            'description' => $validated['description'] ?? null,
            'unit_uuid' => $validated['unit_uuid'],
        ]);

        $product->load('unit');

        return response()->json([
            'message' => 'تم تعديل المنتج بنجاح',
            'data' => $product,
        ]);
    }

    /**
     * حذف منتج
     */
    public function destroy(product $product)
    {
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
            $code = 'PRD-' . strtoupper(Str::random(8));
        } while (product::where('code', $code)->exists());

        return $code;
    }
}