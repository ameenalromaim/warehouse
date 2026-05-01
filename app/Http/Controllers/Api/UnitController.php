<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Units;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * عرض جميع الوحدات
     */
    public function index()
    {
        return response()->json(
            Units::orderBy('name')->get()
        );
    }

    /**
     * إضافة وحدة جديدة
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:units,name',
        ]);

        $unit = Units::create([
            'name' => trim($data['name']),
        ]);

        return response()->json([
            'message' => 'تم إنشاء الوحدة بنجاح',
            'data' => $unit,
        ], 201);
    }

    /**
     * عرض وحدة واحدة
     */
    public function show($id)
    {
        $unit = Units::findOrFail($id);

        return response()->json($unit);
    }

    /**
     * تعديل وحدة
     */
    public function update(Request $request, $id)
    {
        $unit = Units::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $id,
        ]);

        $unit->update([
            'name' => trim($data['name']),
        ]);

        return response()->json([
            'message' => 'تم تعديل الوحدة بنجاح',
            'data' => $unit,
        ]);
    }

    /**
     * حذف وحدة
     */
    public function destroy($id)
    {
        $unit = Units::findOrFail($id);

        $unit->delete();

        return response()->json([
            'message' => 'تم حذف الوحدة بنجاح'
        ]);
    }
}
