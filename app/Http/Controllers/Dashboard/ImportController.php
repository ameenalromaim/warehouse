<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SuppliersImport;
use App\Imports\ProductsImport;
use Throwable;

class ImportController extends Controller
{
    public function index()
    {
        return view('dashboard.import.index');
    }

    public function importSuppliers(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        Excel::import(new SuppliersImport, $request->file('file'));

        return back()->with('success', 'تم استيراد الموردين');
    }

    public function importProducts(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        try {
            Excel::import(new ProductsImport, $request->file('file'));
        } catch (Throwable $e) {
            Log::error('Products import failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->withErrors([
                'file' => 'فشل استيراد ملف الأصناف: ' . $e->getMessage(),
            ]);
        }

        return back()->with('success', 'تم استيراد الأصناف');
    }
}