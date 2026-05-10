<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;

final class BranchData
{
    /**
     * قيمة type_location عند الإنشاء/التعديل: المستخدم العادي يأخذ فرعه؛ السوبر من الطلب.
     */
    public static function locationForWrite(Request $request): ?string
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return null;
        }

        if ($user->isBranchUser()) {
            return $user->branchScopeKey();
        }

        $raw = $request->input('type_location');

        if ($raw === null || $raw === '') {
            return null;
        }

        return trim((string) $raw);
    }
}
