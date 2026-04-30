<?php

namespace App\Imports;

use App\Models\product;
use App\Models\units;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function __construct()
    {
        // حافظ على أسماء الأعمدة كما هي (خصوصاً العربية) بدل تحويلها لـ slug.
        HeadingRowFormatter::default('none');
    }

    public function model(array $row)
    {
        $normalizedRow = $this->normalizeRowKeys($row);

        $productName = $this->getValue($normalizedRow, ['name', 'product_name', 'product', 'اسم الصنف', 'اسم المنتج']);
        $unitName = $this->getValue($normalizedRow, ['unit', 'unit_name', 'الوحدة', 'اسم الوحدة']);
        $code = $this->getValue($normalizedRow, ['id', 'code', 'product_code', 'product_number', 'number', 'رقم المنتج', 'رقم المنتجات', 'رقم الصنف', 'كود', 'الكود']);
        $description = $this->getValue($normalizedRow, ['description', 'desc', 'الوصف']);

        if ($productName === null || $unitName === null || $code === null) {
            return null;
        }

        // منع تكرار الوحدات (مع تجاهل حالة الأحرف والمسافات الزائدة).
        $normalizedUnitName = $this->normalizeText($unitName);
        $unit = units::query()
            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower($normalizedUnitName)])
            ->first();
        if (!$unit) {
            $unit = units::create(['name' => $normalizedUnitName]);
        }

        $product = product::query()->where('code', $code)->first();
        if (!$product) {
            $product = product::firstOrNew(['name' => $productName]);
        }

        $product->name = $productName;
        $product->unit_id = $unit->id;
        $product->description = $description;
        $product->code = $code;

        $product->save();

        return $product;
    }

    private function getValue(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            $normalizedKey = $this->normalizeKey($key);
            if (!array_key_exists($normalizedKey, $row)) {
                continue;
            }

            $value = is_string($row[$normalizedKey]) ? trim($row[$normalizedKey]) : $row[$normalizedKey];
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
            $normalized[$this->normalizeKey((string) $key)] = $value;
        }

        return $normalized;
    }

    private function normalizeKey(string $key): string
    {
        // إزالة BOM من أول عمود في ملفات Excel/CSV إن وجدت.
        $key = str_replace("\xEF\xBB\xBF", '', $key);
        $key = trim(strtolower($key));
        $key = str_replace(['-', '_'], ' ', $key);
        $key = preg_replace('/\s+/u', ' ', $key);

        return $key ?? '';
    }
}
