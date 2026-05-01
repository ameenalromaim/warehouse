<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchasesExport implements FromCollection, WithHeadings
{
    protected $supplier;
    protected $invoice;
    protected $date;
    protected $product;

    public function __construct($supplier = null, $invoice = null, $date = null, $product = null)
    {
        $this->supplier = $supplier;
        $this->invoice  = $invoice;
        $this->date     = $date;
        $this->product  = $product;
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
        $query = Purchase::with([
            'supplier',
            'items.product',
            'items.unit'
        ]);

        // فلتر المورد
        if ($this->supplier) {
            $query->whereHas('supplier', function ($q) {
                $q->where('name', 'like', '%' . $this->supplier . '%');
            });
        }

        // فلتر الفاتورة
        if ($this->invoice) {
            $query->where('invoice_number', 'like', '%' . $this->invoice . '%');
        }

        // فلتر التاريخ
        if ($this->date) {
            $query->whereDate('date', $this->date);
        }

        // فلتر الصنف
        if ($this->product) {
            $query->whereHas('items.product', function ($q) {
                $q->where('name', 'like', '%' . $this->product . '%');
            });
        }

        $purchases = $query->get();

        $data = [];

        foreach ($purchases as $purchase) {
            foreach ($purchase->items as $item) {

                // فلتر الصنف داخل البنود
                if ($this->product) {
                    if (!str_contains(
                        strtolower($item->product?->name ?? ''),
                        strtolower($this->product)
                    )) {
                        continue;
                    }
                }

                $data[] = [
                    $purchase->invoice_number ?? '',
                    $purchase->supplier?->name ?? '',
                    $purchase->date?->format('Y-m-d') ?? '',
                    $item->product?->name ?? '',
                    $item->unit?->name ?? '',
                    number_format((float)$item->quantity, 0, '.', ''),
                ];
            }
        }

        return collect($data);
    }
}