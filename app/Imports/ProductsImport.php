<?php

namespace App\Imports;

use App\Models\product;
use App\Models\units;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function __construct()
    {
        // الحفاظ على أسماء الأعمدة كما هي
        HeadingRowFormatter::default('none');
    }

public function model(array $row)
{
    $normalizedRow = $this->normalizeRowKeys($row);

    $productName = $this->getValue($normalizedRow, [
        'name',
        'product_name',
        'product',
        'اسم الصنف',
        'اسم المنتج'
    ]);

    $unitName = $this->getValue($normalizedRow, [
        'unit',
        'unit_name',
        'الوحدة',
        'اسم الوحدة'
    ]);

    $code = $this->getValue($normalizedRow, [
        'id',
        'code',
        'product_code',
        'product_number',
        'number',
        'رقم المنتج',
        'رقم المنتجات',
        'رقم الصنف',
        'كود',
        'الكود'
    ]);

    $description = $this->getValue($normalizedRow, [
        'description',
        'desc',
        'الوصف'
    ]);

    if ($productName === null || $unitName === null || $code === null) {
        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | الوحدة
    |--------------------------------------------------------------------------
    */

    $normalizedUnitName = $this->normalizeText($unitName);

    $unit = units::withTrashed()
        ->whereRaw('LOWER(TRIM(name)) = ?', [
            strtolower($normalizedUnitName)
        ])
        ->first();

    if ($unit) {

        // إذا كانت محذوفة soft delete
        if ($unit->trashed()) {
            $unit->restore();
        }

    } else {

        $unit = units::create([
            'name' => $normalizedUnitName,
            'user_id' => auth()->id(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | المنتج
    |--------------------------------------------------------------------------
    */

    // البحث بالكود أولاً حتى لو soft delete
    $product = product::withTrashed()
        ->where('code', $code)
        ->first();

    // إذا لم يوجد بالكود ابحث بالاسم
    if (!$product) {

        $product = product::withTrashed()
            ->whereRaw('LOWER(TRIM(name)) = ?', [
                strtolower(trim($productName))
            ])
            ->first();
    }

    // إذا موجود ومحذوف
    if ($product && $product->trashed()) {
        $product->restore();
    }

    // إذا غير موجود نهائياً
    if (!$product) {

        $product = new product();

        $product->uuid = (string) \Illuminate\Support\Str::uuid();
        $product->user_id = auth()->id();
    }

    /*
    |--------------------------------------------------------------------------
    | تحديث البيانات
    |--------------------------------------------------------------------------
    */

    $product->name = trim($productName);
    $product->unit_uuid = $unit->uuid;
    $product->description = $description;
    $product->code = trim($code);

    $product->save();

    return $product;
}

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function getValue(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {

            $normalizedKey = $this->normalizeKey($key);

            if (! array_key_exists($normalizedKey, $row)) {
                continue;
            }

            $value = is_string($row[$normalizedKey])
                ? trim($row[$normalizedKey])
                : $row[$normalizedKey];

            if ($value !== null && $value !== '') {
                return (string) $value;
            }
        }

        return null;
    }

    private function normalizeText(string $value): string
    {
        $value = preg_replace('/\s+/u', ' ', trim($value));

        return $value ?? '';
    }

    private function normalizeRowKeys(array $row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {

            $normalized[
                $this->normalizeKey((string) $key)
            ] = $value;
        }

        return $normalized;
    }

    private function normalizeKey(string $key): string
    {
        // إزالة BOM من ملفات Excel/CSV
        $key = str_replace("\xEF\xBB\xBF", '', $key);

        $key = trim(strtolower($key));

        $key = str_replace(['-', '_'], ' ', $key);

        $key = preg_replace('/\s+/u', ' ', $key);

        return $key ?? '';
    }
}