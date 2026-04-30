<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\suppliers;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // 🔷 عرض الموردين
    public function index(Request $request)
    {
        $query = suppliers::query();

        // 🔍 بحث
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 📄 Pagination
        $suppliers = $query->latest()->paginate(10);

        return response()->json($suppliers);
    }

    // 🔷 إضافة مورد
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
            'data' => $supplier
        ], 201);
    }

    // 🔷 عرض مورد واحد
    public function show($id)
    {
        $supplier = suppliers::findOrFail($id);

        return response()->json($supplier);
    }

    // 🔷 تحديث مورد
    public function update(Request $request, $id)
    {
        $supplier = suppliers::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $supplier->update($request->all());

        return response()->json([
            'message' => 'تم التحديث',
            'data' => $supplier
        ]);
    }

    // 🔷 حذف مورد
    public function destroy($id)
    {
        $supplier = suppliers::findOrFail($id);
        $supplier->delete();

        return response()->json([
            'message' => 'تم الحذف'
        ]);
    }
}
