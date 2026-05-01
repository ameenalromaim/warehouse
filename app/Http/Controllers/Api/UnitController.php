<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\units;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    /**
     * عرض جميع الوحدات
     */
    public function index()
    {
        return response()->json(
            units::orderBy('name')->get()
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

        $unit = units::create([
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
    public function show(units $unit)
    {
        return response()->json($unit);
    }

    /**
     * تعديل وحدة
     */
    public function update(Request $request, units $unit)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('units', 'name')->ignore($unit->uuid, 'uuid')],
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
    public function destroy(units $unit)
    {
        $unit->delete();

        return response()->json([
            'message' => 'تم حذف الوحدة بنجاح',
        ]);
    }
}
