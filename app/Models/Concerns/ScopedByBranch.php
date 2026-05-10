<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait ScopedByBranch
{
    /**
     * يقيّد الاستعلام بفرع المستخدم الحالي (مستخدم فرع) أو يتركه للسوبر أدمن.
     */
    public function scopeForUserBranch(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (! $user instanceof User) {
            return $query;
        }

        if ($user->isSuperAdmin()) {
            return $query;
        }

        $loc = $user->branchScopeKey();

        if ($loc === null) {
            return $query->whereRaw('1 = 0');
        }

        $table = $query->getModel()->getTable();

        return $query->where($table.'.type_location', $loc);
    }
}
