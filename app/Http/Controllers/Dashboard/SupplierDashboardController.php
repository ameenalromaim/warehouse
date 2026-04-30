<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\suppliers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplierDashboardController extends Controller
{
    public function index()
    {
        $suppliers = suppliers::latest()->paginate(10);

        return view('dashboard.suppliers.index', compact('suppliers'));
    }

    public function update(Request $request, suppliers $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $supplier->update($validated);

        return redirect()
            ->route('dashboard.suppliers')
            ->with('success', 'تم تعديل بيانات المورد بنجاح.');
    }

    public function destroy(suppliers $supplier): RedirectResponse
    {
        $supplier->delete();

        return redirect()
            ->route('dashboard.suppliers')
            ->with('success', 'تم حذف المورد بنجاح.');
    }
}
