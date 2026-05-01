<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\ReturnsExport;
use App\Http\Controllers\Controller;
use App\Models\ReturnModel;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReturnReportDashboardController extends Controller
{
    public function normal()
    {
        return $this->reportView('normal', 'تقرير المردود');
    }

    public function damage()
    {
        return $this->reportView('damage', 'تقرير التالف');
    }

    protected function reportView(string $type, string $title)
    {
        $q = ReturnModel::where('type', $type)
            ->with(['items.product', 'items.unit', 'supplier']);

        if (request()->filled('supplier')) {
            $s = request('supplier');
            $q->whereHas('supplier', function ($x) use ($s) {
                $x->where('name', 'like', '%'.$s.'%');
            });
        }

        if (request()->filled('date')) {
            $q->whereDate('date', request('date'));
        }

        if (request()->filled('product')) {
            $p = request('product');
            $q->whereHas('items.product', function ($x) use ($p) {
                $x->where('name', 'like', '%'.$p.'%');
            });
        }

        $returns = $q->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.returns.report', compact('returns', 'type', 'title'));
    }

    public function export(Request $request): BinaryFileResponse
    {
        $data = $request->validate([
            'type' => 'required|in:normal,damage',
            'supplier' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'product' => 'nullable|string|max:255',
        ]);

        $filename = $data['type'] === 'damage' ? 'taalif-report.xlsx' : 'mardoud-report.xlsx';

        return Excel::download(
            new ReturnsExport(
                $data['type'],
                $data['supplier'] ?? null,
                $data['date'] ?? null,
                $data['product'] ?? null
            ),
            $filename
        );
    }
}
