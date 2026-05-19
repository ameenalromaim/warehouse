<?php

namespace App\Support;

final class PhoneNormalizer
{
    /**
     * توحيد رقم الهاتف للتخزين والبحث:
     * 0771738225، +967 771 738 225، 773 503 688 → 771738225 / 773503688
     */
    public static function normalize(string $input): string
    {
        $input = self::convertArabicDigitsToWestern($input);
        $digits = preg_replace('/\D+/', '', $input) ?? '';

        while (strlen($digits) > 12 && str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            return substr($digits, 1);
        }

        if (strlen($digits) === 12 && str_starts_with($digits, '967')) {
            return substr($digits, 3);
        }

        if (strlen($digits) === 12 && str_starts_with($digits, '966')) {
            return substr($digits, 3);
        }

        return $digits;
    }

    public static function convertArabicDigitsToWestern(string $value): string
    {
        static $map = [
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ];

        return strtr($value, $map);
    }
}
