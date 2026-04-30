<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\product;
use App\Models\units;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductDashboardController extends Controller
{
    public function index()
    {
        $products = product::with('unit')->latest()->paginate(10);
        $units = units::orderBy('name')->get();

        return view('dashboard.products.index', compact('products', 'units'));
    }

    public function update(Request $request, product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255', Rule::unique('product', 'code')->ignore($product->id)],
            'description' => ['nullable', 'string'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
        ]);

        $product->update($validated);

        return redirect()
            ->route('dashboard.products')
            ->with('success', 'تم تعديل بيانات الصنف بنجاح.');
    }

    public function destroy(product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('dashboard.products')
            ->with('success', 'تم حذف الصنف بنجاح.');
    }
}
