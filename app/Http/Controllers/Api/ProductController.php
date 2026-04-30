<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
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

        $row = product::create([
            'name' => $validated['name'],
            'code' => $code,
            'description' => $validated['description'] ?? null,
            'unit_id' => $validated['unit_id'],
        ]);

        $row->load('unit');

        return response()->json([
            'message' => 'Product created',
            'data' => $row,
        ], 201);
    }

    public function index()
    {
        return response()->json(
            product::with('unit')->latest()->get()
        );
    }

    private function generateUniqueProductCode(): string
    {
        do {
            $code = 'PRD-'.strtoupper(Str::random(8));
        } while (product::where('code', $code)->exists());

        return $code;
    }
}
