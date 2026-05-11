<?php
namespace App\Imports;

use App\Models\suppliers;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SuppliersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $supplier = suppliers::firstOrNew(
            ['name' => $row['name']],
        );

        $supplier->fill([
            'phone' => $row['phone'] ?? null,
            'address' => $row['address'] ?? null,
            'note' => $row['note'] ?? null,
        ]);

        if (! $supplier->exists && auth()->check()) {
            $supplier->user_id = auth()->id();
        }

        $supplier->save();

        return $supplier;
    }
}
