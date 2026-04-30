<?php
namespace App\Imports;

use App\Models\suppliers;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SuppliersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return suppliers::updateOrCreate(
            ['name' => $row['name']], // مفتاح منع التكرار
            [
                'phone'   => $row['phone'] ?? null,
                'address' => $row['address'] ?? null,
                'note'    => $row['note'] ?? null,
            ]
        );
    }
}
