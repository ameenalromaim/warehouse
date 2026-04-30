<?php

namespace App\Exports;

use App\Models\purchaseitem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseItemLineExport implements FromCollection, WithHeadings
{
    public function __construct(
        private purchaseitem $item
    ) {
    }

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
        $this->item->loadMissing(['purchase.supplier', 'product', 'unit']);
        $purchase = $this->item->purchase;

        return collect([[
            $purchase?->invoice_number ?? '',
            $purchase?->supplier?->name ?? '',
            $purchase?->date?->format('Y-m-d') ?? '',
            $this->item->product?->name ?? '',
            $this->item->unit?->name ?? '',
            number_format((float) $this->item->quantity, 0, '.', ''),
        ]]);
    }
}
