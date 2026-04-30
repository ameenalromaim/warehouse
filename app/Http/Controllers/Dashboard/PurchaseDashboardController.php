<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\purchaseitem;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchasesExport;
use App\Exports\PurchaseSingleExport;
use App\Exports\PurchaseItemLineExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PurchaseDashboardController extends Controller
{
    // 📊 عرض الداشبورد
    // public function index()
    // {
    //     $purchases = Purchase::with('supplier')
    //         ->latest()
    //         ->paginate(10);

    //     return view('dashboard.purchases.index', compact('purchases'));
    // }
    public function index()
{
    $purchases = Purchase::with([
        'supplier',
        'items.product',
        'items.unit'
    ])->latest()->paginate(10);

    return view('dashboard.purchases.index', compact('purchases'));
}

    // 📥 تصدير Excel
    public function export()
    {
        return Excel::download(new PurchasesExport, 'purchases.xlsx');
    }

    public function exportOne(Purchase $purchase): BinaryFileResponse
    {
        $purchase->load(['supplier', 'items.product', 'items.unit']);

        $slug = $purchase->invoice_number ?: (string) $purchase->id;
        $slug = preg_replace('/[^A-Za-z0-9._-]+/u', '_', $slug) ?: (string) $purchase->id;
        $filename = 'purchase-'.$slug.'.xlsx';

        return Excel::download(new PurchaseSingleExport($purchase), $filename);
    }

    public function exportItem(purchaseitem $purchaseitem): BinaryFileResponse
    {
        $purchaseitem->load(['purchase.supplier', 'product', 'unit']);

        $filename = 'purchase-'.$purchaseitem->purchase_id.'-line-'.$purchaseitem->id.'.xlsx';

        return Excel::download(new PurchaseItemLineExport($purchaseitem), $filename);
    }
}
