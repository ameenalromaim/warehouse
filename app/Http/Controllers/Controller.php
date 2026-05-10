<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class Controller
{
    protected function authorizeBranchRecord(?Model $model): void
    {
        if ($model === null) {
            return;
        }

        $user = request()->user();

        if (! $user instanceof User || $user->isSuperAdmin()) {
            return;
        }

        if (! array_key_exists('type_location', $model->getAttributes())) {
            return;
        }

        if (! $user->canAccessBranchRecord($model->getAttribute('type_location'))) {
            abort(403, __('غير مصرح بالوصول لهذا السجل.'));
        }
    }
}
