<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\PurchaseItemLineExport;
use App\Exports\PurchasesExport;
use App\Exports\PurchaseSingleExport;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\purchaseitem;
use Maatwebsite\Excel\Facades\Excel;
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
            'creator',
            'items.product',
            'items.unit',
        ])
            ->forUserBranch(auth()->user())
            ->latest()
            ->paginate(10);

        return view('dashboard.purchases.index', compact('purchases'));
    }

    // 📥 تصدير Excel
    public function export()
    {
        return Excel::download(
            new PurchasesExport(
                request('supplier'),
                request('invoice'),
                request('date'),
                request('product')
            ),
            'purchases.xlsx'
        );
    }

    public function exportOne(Purchase $purchase): BinaryFileResponse
    {
        $this->authorizeBranchRecord($purchase);

        $purchase->load(['supplier', 'items.product', 'items.unit']);

        $slug = $purchase->invoice_number ?: (string) $purchase->id;
        $slug = preg_replace('/[^A-Za-z0-9._-]+/u', '_', $slug) ?: (string) $purchase->id;
        $filename = 'purchase-'.$slug.'.xlsx';

        return Excel::download(new PurchaseSingleExport($purchase), $filename);
    }

    public function exportItem(purchaseitem $purchaseitem): BinaryFileResponse
    {
        $purchaseitem->load(['purchase.supplier', 'product', 'unit']);

        $this->authorizeBranchRecord($purchaseitem->purchase);

        $filename = 'purchase-'.substr($purchaseitem->purchase_uuid, 0, 8).'-line-'.$purchaseitem->id.'.xlsx';

        return Excel::download(new PurchaseItemLineExport($purchaseitem), $filename);
    }
}
