<?php

namespace App\Imports;

use App\Models\suppliers;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SuppliersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // التحقق من وجود عمود name
        if (! isset($row['name']) || empty(trim($row['name']))) {

            throw ValidationException::withMessages([
                'file' => ['فشل استيراد الملف. تأكد من تنسيق ملف الإكسل.'],
            ]);
        }

        $supplier = suppliers::withTrashed()->firstOrNew([
            'name' => trim($row['name']),
        ]);

        // إذا كان محذوف soft delete يرجعه
        if ($supplier->trashed()) {
            $supplier->restore();
        }

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