<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'unit_id' => 'required|integer|exists:units,id',
        ]);

        $code = $validated['code'] ?? null;

        if ($code === null || $code === '') {
            $code = $this->generateUniqueProductCode();
        }

        $row = Product::create([
            'name' => $validated['name'],
            'code' => $code,
            'description' => $validated['description'] ?? null,
            'unit_id' => $validated['unit_id'],
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
    public function show($id)
    {
        $row = Product::with('unit')->findOrFail($id);

        return response()->json($row);
    }

    /**
     * تعديل منتج
     */
    public function update(Request $request, $id)
    {
        $row = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255|unique:product,code,' . $id,
            'description' => 'nullable|string',
            'unit_id' => 'required|integer|exists:units,id',
        ]);

        $code = $validated['code'] ?? null;

        if ($code === null || $code === '') {
            $code = $row->code ?: $this->generateUniqueProductCode();
        }

        $row->update([
            'name' => $validated['name'],
            'code' => $code,
            'description' => $validated['description'] ?? null,
            'unit_id' => $validated['unit_id'],
        ]);

        $row->load('unit');

        return response()->json([
            'message' => 'تم تعديل المنتج بنجاح',
            'data' => $row,
        ]);
    }

    /**
     * حذف منتج
     */
    public function destroy($id)
    {
        $row = Product::findOrFail($id);

        $row->delete();

        return response()->json([
            'message' => 'تم حذف المنتج بنجاح'
        ]);
    }

    /**
     * توليد كود منتج تلقائي
     */
    private function generateUniqueProductCode(): string
    {
        do {
            $code = 'PRD-' . strtoupper(Str::random(8));
        } while (Product::where('code', $code)->exists());

        return $code;
    }
}