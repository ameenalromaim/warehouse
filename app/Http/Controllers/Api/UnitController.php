<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\units;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        return response()->json(units::orderBy('name')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:units,name',
        ]);

        $unit = units::create([
            'name' => trim($data['name']),
        ]);

        return response()->json([
            'message' => 'Unit created',
            'data' => $unit,
        ], 201);
    }
}
