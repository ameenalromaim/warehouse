<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchasesExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return [
            'رقم الفاتورة',
            'اسم المورد',
            'التاريخ',
            'الصنف',
            'الوحدة',
            'الكمية',
        ];
    }

    public function collection()
    {
        $data = [];

        $purchases = Purchase::with([
            'supplier',
            'items.product',
            'items.unit'
        ])->get();

        foreach ($purchases as $purchase) {
            foreach ($purchase->items as $item) {
                $data[] = [
                    $purchase->invoice_number ?? '',
                    $purchase->supplier?->name ?? '',
                    $purchase->date?->format('Y-m-d') ?? '',
                    $item->product?->name ?? '',
                    $item->unit?->name ?? '',
                    number_format((float) $item->quantity, 0, '.', ''),
                ];
            }
        }

        return collect($data);
    }
}