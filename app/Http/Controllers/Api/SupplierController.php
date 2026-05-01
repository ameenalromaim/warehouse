<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\suppliers;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = suppliers::query();

        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $suppliers = $query->latest()->paginate(10);

        return response()->json($suppliers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $supplier = suppliers::create($request->only('name', 'phone', 'address', 'note'));

        return response()->json([
            'message' => 'تم إضافة المورد',
            'data' => $supplier,
        ], 201);
    }

    public function show(suppliers $supplier)
    {
        return response()->json($supplier);
    }

    public function update(Request $request, suppliers $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $supplier->update($request->all());

        return response()->json([
            'message' => 'تم التحديث',
            'data' => $supplier,
        ]);
    }

    public function destroy(suppliers $supplier)
    {
        $supplier->delete();

        return response()->json([
            'message' => 'تم الحذف',
        ]);
    }
}
