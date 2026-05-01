<?php

namespace App\Exports;

use App\Models\ReturnModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReturnsExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected string $type,
        protected ?string $supplier = null,
        protected ?string $date = null,
        protected ?string $product = null
    ) {}

    public function headings(): array
    {
        return [
            'رقم الحركة',
            'التاريخ',
            'المورد',
            'النوع',
            'الصنف',
            'الوحدة',
            'الكمية',
            'ملاحظات',
        ];
    }

    public function collection()
    {
        $query = ReturnModel::with(['items.product', 'items.unit', 'supplier'])
            ->where('type', $this->type);

        if ($this->supplier) {
            $query->whereHas('supplier', function ($q) {
                $q->where('name', 'like', '%'.$this->supplier.'%');
            });
        }

        if ($this->date) {
            $query->whereDate('date', $this->date);
        }

        if ($this->product) {
            $query->whereHas('items.product', function ($q) {
                $q->where('name', 'like', '%'.$this->product.'%');
            });
        }

        $typeLabel = $this->type === 'damage' ? 'تالف' : 'مردود';

        $data = [];
        foreach ($query->orderByDesc('date')->orderByDesc('id')->get() as $return) {
            foreach ($return->items as $item) {
                if ($this->product && ! str_contains(
                    mb_strtolower($item->product?->name ?? ''),
                    mb_strtolower($this->product)
                )) {
                    continue;
                }

                $data[] = [
                    $return->id,
                    $return->date?->format('Y-m-d') ?? '',
                    $return->supplier?->name ?? '',
                    $typeLabel,
                    $item->product?->name ?? '',
                    $item->unit?->name ?? '',
                    number_format((float) $item->quantity, 2, '.', ''),
                    $return->note ?? '',
                ];
            }
        }

        return collect($data);
    }
}
