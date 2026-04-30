<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseSingleExport implements FromCollection, WithHeadings
{
    public function __construct(
        private Purchase $purchase
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
        $this->purchase->loadMissing([
            'supplier',
            'items.product',
            'items.unit',
        ]);

        $data = [];

        foreach ($this->purchase->items as $item) {
            $data[] = [
                $this->purchase->invoice_number ?? '',
                $this->purchase->supplier?->name ?? '',
                $this->purchase->date?->format('Y-m-d') ?? '',
                $item->product?->name ?? '',
                $item->unit?->name ?? '',
                number_format((float) $item->quantity, 0, '.', ''),
            ];
        }

        return collect($data);
    }
}
