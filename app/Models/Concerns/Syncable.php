<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Soft deletes + sync-oriented versioning + UUID route binding.
 *
 * Add casts for deleted_at, synced_at, version in each model's casts() / $casts.
 */
trait Syncable
{
    use SoftDeletes;

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public static function bootSyncable(): void
    {
        static::updating(function ($model) {
            $dirty = collect($model->getDirty())->except(['updated_at']);
            if ($dirty->isEmpty()) {
                return;
            }

            if ($dirty->keys()->diff(['synced_at'])->isEmpty()) {
                return;
            }

            $dirty = $dirty->except(['synced_at']);
            if ($dirty->keys()->diff(['version'])->isEmpty()) {
                return;
            }

            $model->version = ((int) $model->getOriginal('version')) + 1;
        });
    }
}
